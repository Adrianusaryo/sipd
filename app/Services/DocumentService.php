<?php

namespace App\Services;

use App\Events\DocumentStatusUpdateEvent;
use App\Jobs\ProcessApplicantDocumentJob;
use App\Models\Document;
use App\Models\User;
use App\Notifications\DocumentStatusUpdatedNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class DocumentService
{
    protected string $cacheKey = 'applicant_document_all';

    private function clearDocumentCache(): void
    {
        Cache::forget($this->cacheKey);
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
                $filePath = $file->store('attachment', 'local');
                $request->files()->create([
                    'document_type' => $data['document_type'] ?? 'attachment',
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_mime' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'version' => 1,
                ]);
            }

            // Clear cache
            $this->clearDocumentCache();

            return $request;
        });

        ProcessApplicantDocumentJob::dispatch($result);

        // Notifikasi & Reverb Broadcast
        $verificators = User::role('verificator', 'api')->get();
        Notification::send($verificators, new DocumentStatusUpdatedNotification($result, 'submitted'));
        foreach ($verificators as $verificator) {
            DocumentStatusUpdateEvent::dispatch(
                $result,
                $verificator->id,
                'Pemohon telah mengajukan dokumen {$result->title}.'
            );
        }

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
                foreach ($files as $file) {
                    $lastVersion = $document->files()->max('version') ?? 1;
                    $newVersion = $lastVersion + 1;

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

        ProcessApplicantDocumentJob::dispatch($document);

        return $document->load(['project', 'files']);
    }

    public function updateByVerificator(Document $document, array $data, User $verificator): Document
    {
        return DB::transaction(function () use ($document, $data, $verificator) {
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

            return $document->load(['project', 'files', 'verificator']);
        });
    }

    // public function showAllRequest(): array
    // {
    //     return Cache::remember($this->cacheKey, now()->addDay(), function () {
    //         return ApplicantDocument::with(['project', 'files', 'applicant'])->latest()->get()->toArray();
    //     });
    // }
}
