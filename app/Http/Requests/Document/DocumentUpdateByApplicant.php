<?php

namespace App\Http\Requests\Document;

use App\Enums\DocumentStatus;
use Illuminate\Foundation\Http\FormRequest;

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
            'project_id' => ['sometimes', 'required', 'exists:projects,id'],
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
