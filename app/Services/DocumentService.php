<?php

namespace App\Services;

use App\Events\DocumentResubmittedEvent;
use App\Events\DocumentStatusUpdatedEvent;
use App\Events\DocumentSubmittedEvent;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentService
{
    protected string $cacheKey = 'applicant_document_all';

    private function clearDocumentCache(): void
    {
        DB::table('cache')->where('key', 'like', config('cache.prefix').$this->cacheKey.'%')->delete();
    }

    public function showDocumentRequest(int $perPage = 10, int $page = 1): array
    {
        $dynamicKey = "{$this->cacheKey}_page_{$page}_per_{$perPage}";

        return Cache::remember($dynamicKey, now()->addHours(1), function () use ($perPage) {
            $paginator = Document::with(['project', 'files', 'applicant'])->latest()->paginate($perPage);

            return [
                'items' => array_map(fn ($item) => $item->toArray(), $paginator->items()),
                'total' => $paginator->total(),
                'perPage' => $paginator->perPage(),
                'currentPage' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
            ];
        });
    }

    public function createRequest(array $data, array $files, User $user): Document
    {
        $result = DB::transaction(function () use ($data, $files, $user) {
            $registrationNumber = 'REG-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));

            $request = Document::create([
                'number_registration' => $registrationNumber,
                'applicant_id' => $user->id,
                'project_id' => $data['project_id'],
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

            return $request;
        });

        // Clear Cache
        $this->clearDocumentCache();

        // Queue Job
        // ProcessDocumentJob::dispatch($result, 'created');

        // Notifikasi & Reverb Broadcast
        $verificators = User::role('verificator', 'api')->get();
        // Notification::send($verificators, new DocumentStatusUpdatedNotification($result, 'submitted'));

        DocumentSubmittedEvent::dispatch(
            $result, "Applicant has make a document request {$result->title}."
        );

        return $result->load(['project', 'files']);
    }

    public function updateByApplicant(Document $document, array $data, ?array $files = null): Document
    {
        DB::transaction(function () use ($document, $data, $files) {
            $document->update([
                'project_id' => $data['project_id'] ?? $document->project_id,
                'title' => $data['title'] ?? $document->title,
                'description' => $data['description'] ?? $document->description,
                'status' => 'submitted',
            ]);

            if (! empty($files)) {
                $lastVersion = $document->files()->max('version') ?? 1;
                $newVersion = $lastVersion + 1;

                foreach ($files as $file) {

                    $filePath = $file->store('attachment', 'local');

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

        });

        $this->clearDocumentCache();

        DocumentResubmittedEvent::dispatch(
            $document, "Applicant has updated and resubmitted the document {$document->title}."
        );

        return $document->load(['project', 'files']);
    }

    public function updateByVerificator(Document $document, array $data, User $verificator): Document
    {
        DB::transaction(function () use ($document, $data, $verificator) {
            $oldStatus = $document->status;
            $newStatus = $data['status'];

            $updateData = [
                'status' => $newStatus,
                'verificator_id' => $verificator->id,
                'verificator_notes' => $data['notes'] ?? null,
            ];

            if ($newStatus === 'approved') {
                $updateData['approved_at'] = now();
            }

            $document->update($updateData);

        });

        $this->clearDocumentCache();

        DocumentStatusUpdatedEvent::dispatch(
            $document, "Verificator has updated status document {$document->title}."
        );

        return $document->load(['project']);
    }
}
