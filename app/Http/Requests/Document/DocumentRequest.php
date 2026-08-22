<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

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
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'document_type' => ['nullable', 'string', 'max:50'],

            // Validasi Array File Upload
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
