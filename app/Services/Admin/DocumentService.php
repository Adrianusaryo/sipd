<?php

namespace App\Services\Admin;

use App\Events\DocumentStatusUpdatedEvent;
use App\Helpers\DocumentHelper;
use App\Helpers\PaginationHelper;
use App\Models\Document;
use App\Models\User;
use App\Services\ApprovalLogsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DocumentService
{
    protected string $cacheKey = 'show_document';

    public function __construct(protected ApprovalLogsService $logService) {}

    private function clearDocumentCache(): void
    {
        DB::table('cache')->where('key', 'like', config('cache.prefix').$this->cacheKey.'%')->delete();
    }

    private function getPaginatedDocuments(string $cacheKey, int $limit, array $relations = [], ?int $userId = null): array
    {
        return Cache::remember($cacheKey, now()->addHours(1), function () use ($limit, $relations) {
            $paginator = Document::with($relations)
                ->latest()->paginate($limit);

            return PaginationHelper::format($paginator);
        });
    }

    public function showDocument(int $limit = 10, int $page = 1): array
    {
        return $this->getPaginatedDocuments(
            cacheKey: "{$this->cacheKey}_page_{$page}_per_{$limit}",
            limit: $limit,
            relations: ['files', 'user']
        );
    }

    public function showSummary(): array
    {
        $cacheKey = "{$this->cacheKey}_summary_admin";

        return Cache::remember($cacheKey, now()->addDays(1), function () {
            return DocumentHelper::getSummary();
        });
    }

    public function updateDocument(Document $document, array $data, User $admin): Document
    {
        DB::transaction(function () use ($document, $data, $admin) {
            $newStatus = $data['status'];

            $updateData = [
                'status' => $newStatus,
                'admin_id' => $admin->id,
                'admin_notes' => $data['admin_notes'] ?? null,
            ];

            if ($newStatus === 'approved') {
                $updateData['approved_at'] = now();
            }

            $document->update($updateData);

            $document->approvalLogs()->create([
                'actor_id' => $admin->id,
                'status' => $newStatus,
                'notes' => 'Update Status Document',
            ]);

        });

        $this->clearDocumentCache();
        $this->logService->clearLogCache();

        DocumentStatusUpdatedEvent::dispatch(
            $document, "Admin has updated status document {$document->title}."
        );

        return $document->load(['approvalLogs', 'admin']);
    }
}
