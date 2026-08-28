<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProjectRequest',
    title: 'Project Request',
    description: false,
    properties: [
        new OA\Property(property: 'title', type: 'string', example: 'Kampung Nelayan Merah Putih'),
        new OA\Property(property: 'description', type: 'string', example: 'Pengajuan izin analisis dampak pembangunan Kampung Nelayan Merah Putih.'),
    ]
)]

class ProjectRequest extends FormRequest
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

    // ', '', '', 'is_active
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['string', 'nullable'],
        ];
    }
}
