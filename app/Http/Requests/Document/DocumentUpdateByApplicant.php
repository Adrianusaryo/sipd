<?php

namespace App\Http\Requests\Document;

use App\Enums\DocumentStatus;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DocumentUpdateRequestApplicant',
    title: 'Document Request',
    description: false,
    required: ['project_id', 'title', 'files[]'],
    properties: [
        new OA\Property(
            property: 'project_id',
            type: 'integer',
            example: 1,
            description: 'ID Project terkait'
        ),
        new OA\Property(
            property: 'title',
            type: 'string',
            example: 'Dokumen AMDAL Tahap 1',
            description: 'Judul Dokumen'
        ),
        new OA\Property(
            property: 'description',
            type: 'string',
            nullable: true,
            example: 'Pengajuan dokumen AMDAL untuk kawasan pesisir.',
            description: 'Deskripsi tambahan (opsional)'
        ),
        new OA\Property(
            property: 'document_type',
            type: 'string',
            nullable: true,
            example: 'AMDAL',
            description: 'Tipe Dokumen (opsional)'
        ),
        // Array File Upload
        new OA\Property(
            property: 'files[]',
            type: 'array',
            description: 'Upload 1 atau lebih file (pdf, jpg, jpeg, png, docx. Max: 5MB per file)',
            items: new OA\Items(
                type: 'string',
                format: 'binary'
            )
        ),
    ],
)]

class DocumentUpdateByApplicant extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $document = $this->route('document');

        return $document->applicant_id === $this->user()->id && in_array($document->status, [DocumentStatus::REVISION, DocumentStatus::REJECTED]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'document_type' => ['nullable', 'string', 'max:50'],
            'files' => ['nullable', 'array'],
            'files.*' => [
                'required', 'file', 'mimes:pdf,doc,docx,png,jpg,jpeg',
                'max:10240',
            ],
        ];
    }
}
