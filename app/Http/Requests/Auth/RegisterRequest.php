<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RegisterRequest',
    title: 'Register Request',
    description: false,
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Joko Widodo'),
        new OA\Property(property: 'email', type: 'string', example: 'jokowi@gmail.com'),
        new OA\Property(property: 'password', type: 'string', example: 'Password123$'),
        new OA\Property(property: 'password_confirmation', type: 'string', example: 'Password123$'),
        new OA\Property(property: 'nip_nik', type: 'string', example: '199001012020121004'),
        new OA\Property(property: 'phone', type: 'string', example: '081234567895'),
    ]
)]

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'confirmed'],
            'nip_nik' => ['required', 'string', 'numeric', 'unique:users,nip_nik'],
            'phone' => ['required', 'string', 'max:255', 'unique:users,phone'],
        ];
    }
}
