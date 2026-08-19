<?php

namespace App\Services;

use App\Models\ApplicantDocument;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use illuminate\Support\Str;

class ApplicantDocumentService
{
    public function createRequest(array $data, array $files, User $user): ApplicantDocument
    {
        return DB::transaction(function () use ($data, $files, $user) {
            $registrationNumber = 'REG-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));

            $request = ApplicantDocument::create([
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

            return $request->load(['project', 'files']);
        });
    }
}
