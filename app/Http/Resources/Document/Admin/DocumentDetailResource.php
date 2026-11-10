<?php

namespace App\Http\Resources\Document\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AdminDocumentDetailResource',
    type: 'object',
    properties: [
        new OA\Property(
            property: 'document',
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'username', type: 'string', example: 'Joko Widodo'),
                new OA\Property(property: 'number_registration', type: 'string', example: 'REG-20261102-XT0CH'),
                new OA\Property(property: 'title', type: 'string', example: 'Pengajuan Dokumen Karhutla Kalimantan'),
                new OA\Property(property: 'description', type: 'string', example: 'Berkas analisis dokumen'),
                new OA\Property(property: 'status', type: 'string', example: 'submitted'),
                new OA\Property(property: 'submitted_at', type: 'string', format: 'date-time', example: '2026-11-05T14:08:29.000000Z'),
            ]
        ),
        new OA\Property(
            property: 'files',
            type: 'array',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(property: 'document_id', type: 'integer', example: 1),
                    new OA\Property(property: 'document_type', type: 'string', example: 'attachment'),
                    new OA\Property(property: 'file_url', type: 'string', example: 'http://localhost:8000/storage/attachment/sample.png'),
                    new OA\Property(property: 'file_name', type: 'string', example: 'Screenshot 2026-11-05.png'),
                    new OA\Property(property: 'version', type: 'integer', example: 1),
                ]
            )
        ),
    ]
)]

class DocumentDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'document' => [
                'id' => $this->id,
                'username' => $this->user->username,
                'number_registration' => $this->number_registration,
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status,
                'submitted_at' => $this->submitted_at,
            ],
            'files' => $this->files->map(function ($file) {
                return [
                    'document_id' => $file->document_id,
                    'document_type' => $file->document_type,
                    'file_url' => asset('storage/'.$file->file_path),
                    'file_name' => $file->file_name,
                    'version' => $file->version,
                ];
            }),

        ];

    }
}
