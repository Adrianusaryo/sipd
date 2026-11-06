<?php

namespace App\Http\Requests\Document\User;

use App\Enums\DocumentStatus;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserDocumentUpdateRequest',
    title: 'Document Update Request By User',
    description: false,
    required: ['document_id', 'files[]'],
    properties: [
        new OA\Property(
            property: 'document_id',
            type: 'integer',
            example: 1,
            description: 'Dokumen ID yang akan diperbarui'
        ),
        new OA\Property(
            property: 'title',
            type: 'string',
            nullable: true,
            example: 'Dokumen AMDAL Tahap 1',
            description: 'Judul Dokumen'
        ),
        new OA\Property(
            property: 'description',
            type: 'string',
            nullable: true,
            example: 'Pengajuan dokumen AMDAL untuk kawasan pesisir.',
            description: 'Deskripsi Dokumen'
        ),
        new OA\Property(
            property: 'document_type',
            type: 'string',
            nullable: true,
            example: 'AMDAL',
            description: 'Jenis Dokumen'
        ),
        new OA\Property(
            property: 'files[]',
            type: 'array',
            description: 'Daftar file dokumen yang diunggah (maksimal 5MB per file)',
            items: new OA\Items(
                type: 'string',
                format: 'binary'
            )
        ),
    ],
)]

class DocumentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $document = $this->route('document');

        return $document->user_id === $this->user()->id && $document->status === DocumentStatus::REVISION;
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
            'files' => ['array', 'min:1'],
            'files.*' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,docx',
                'max:5120',
            ],
        ];
    }
}
