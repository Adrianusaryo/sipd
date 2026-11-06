<?php

namespace App\Http\Requests\Document\User;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DocumentRequest',
    title: 'Document Request',
    description: false,
    required:['title','files'],
    properties: [
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
            description: 'Deskripsi Dokumen'
        ),
        new OA\Property(
            property: 'document_type',
            type: 'string',
            example: 'PDF',
            description: 'Jenis Dokumen'
        ),
        new OA\Property(
            property: 'files',
            type: 'array',
            description: 'Daftar file dokumen yang diunggah (maksimal 5MB per file)',
            items: new OA\Items(
                type: 'string',
                format: 'binary'
            )
        ),
    ],
)]

class DocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'document_type' => ['nullable', 'string', 'max:50'],

            'files' => ['required', 'array', 'min:1'],
            'files.*' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,docx',
                'max:5120',
            ],
        ];
    }
}
