<?php

namespace App\Http\Requests\Document;

use App\Enums\DocumentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DocumentUpdateRequestVerificator',
    title: 'Document Update Request Verificator',
    properties: [
        new OA\Property(
            property: 'status',
            type: 'string',
            enum: [DocumentStatus::APPROVED->value, DocumentStatus::REVISION->value, DocumentStatus::REJECTED->value],
            example: 'approved',
        ),
        new OA\Property(
            property: 'verificator_notes',
            type: 'string',
            nullable: true,
            example: 'Dokumen AMDAL sudah lengkap dan disetujui.',
        ),
    ]
)]

class DocumentUpdateByVerificator extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $document = $this->route('document');

        return $this->user()->hasRole('verificator', 'api') && $document && $document->status === DocumentStatus::SUBMITTED;
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
            'verificator_notes' => ['required_if:status,revision,rejected', 'nullable', 'string', 'max:1000'],
        ];
    }
}
