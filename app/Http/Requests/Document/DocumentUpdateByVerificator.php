<?php

namespace App\Http\Requests\Document;

use App\Enums\DocumentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

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
