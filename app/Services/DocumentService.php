<?php

namespace App\Services;

use App\Events\DocumentResubmittedEvent;
use App\Events\DocumentStatusUpdatedEvent;
use App\Events\DocumentSubmittedEvent;
use App\Helpers\DocumentHelper;
use App\Helpers\PaginationHelper;
use App\Models\Document;
use App\Models\User;
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
        return Cache::remember($cacheKey, now()->addHours(1), function () use ($limit, $relations, $userId) {
            $paginator = Document::with($relations)->when($userId, fn ($query) => $query->where('user_id', $userId))
                ->latest()->paginate($limit);

            return PaginationHelper::format($paginator);
        });
    }

    public function showDocumentByAdmin(int $limit = 10, int $page = 1): array
    {
        return $this->getPaginatedDocuments(
            cacheKey: "{$this->cacheKey}_page_{$page}_per_{$limit}",
            limit: $limit,
            relations: ['files', 'user']
        );
    }

    public function showDocumentByUser(int $userId, int $limit = 10, int $page = 1): array
    {
        return $this->getPaginatedDocuments(
            cacheKey: "{$this->cacheKey}_user_{$userId}_page_{$page}_per_{$limit}",
            limit: $limit,
            relations: ['files'],
            userId: $userId
        );
    }

    public function createRequest(array $data, array $files, User $user): Document
    {
        $result = DB::transaction(function () use ($data, $files, $user) {
            $registrationNumber = DocumentHelper::registrationNumber();

            $request = Document::create([
                'number_registration' => $registrationNumber,
                'user_id' => $user->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            foreach ($files as $file) {
                $filePath = $file->store('attachment', 'public');
                $request->files()->create([
                    'document_type' => $data['document_type'] ?? 'attachment',
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_mime' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'version' => 1,
                ]);
            }

            $request->approvalLogs()->create([
                'actor_id' => $user->id,
                'status' => 'submitted',
                'notes' => 'Create new request document',
            ]);

            return $request;
        });

        // Clear Cache
        $this->clearDocumentCache();
        $this->logService->clearLogCache();

        // Queue Job
        // ProcessDocumentJob::dispatch($result, 'created');

        // Notifikasi & Reverb Broadcast
        $admin = User::role('admin', 'api')->get();
        // Notification::send($verificators, new DocumentStatusUpdatedNotification($result, 'submitted'));

        DocumentSubmittedEvent::dispatch(
            $result, "Applicant has make a document request {$result->title}."
        );

        return $result->load(['files']);
    }

    public function updateByUser(Document $document, array $data, ?array $files, User $user): Document
    {
        DB::transaction(function () use ($document, $data, $files, $user) {
            $document->update([
                'title' => $data['title'] ?? $document->title,
                'description' => $data['description'] ?? $document->description,
                'status' => 'submitted',
            ]);

            if (! empty($files)) {
                $lastVersion = $document->files()->max('version') ?? 1;
                $newVersion = $lastVersion + 1;

                foreach ($files as $file) {

                    $filePath = $file->store('attachment', 'public');

                    $document->files()->create([
                        'document_type' => $data['document_type'] ?? 'attachment',
                        'file_path' => $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_mime' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'version' => $newVersion,
                    ]);
                }
            }

            $document->approvalLogs()->create([
                'actor_id' => $user->id,
                'status' => 'submitted',
                'notes' => 'User has resubmitted document request.',
            ]);

        });

        $this->clearDocumentCache();
        $this->logService->clearLogCache();

        // DocumentResubmittedEvent::dispatch(
        //     $document, "Applicant has updated and resubmitted the document {$document->title}."
        // );

        return $document->load(['files']);
    }

    public function updateByAdmin(Document $document, array $data, User $admin): Document
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
                'notes' => 'Document status was updated.',
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
