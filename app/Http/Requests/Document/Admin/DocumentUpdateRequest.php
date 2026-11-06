<?php

namespace App\Http\Requests\Document\Admin;

use App\Enums\DocumentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AdminDocumentUpdateRequest',
    title: 'Document Update Request By Admin',
    required: ['status','admin_notes'],
    properties: [
        new OA\Property(
            property: 'status',
            type: 'string',
            enum: [DocumentStatus::APPROVED->value, DocumentStatus::REVISION->value, DocumentStatus::REJECTED->value],
            example: 'approved',
        ),
        new OA\Property(
            property: 'admin_notes',
            type: 'string',
            example: 'Dokumen sudah lengkap dan disetujui.',
        ),
    ]
)]

class DocumentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $document = $this->route('id');

        return $this->user()->hasRole('admin', 'api') && $document && $document->status === DocumentStatus::SUBMITTED;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(DocumentStatus::class)],
            'admin_notes' => ['required_if:status,revision,rejected', 'nullable', 'string', 'max:1000'],
        ];
    }
}
