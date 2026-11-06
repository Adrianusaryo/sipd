<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RegisterResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'username', type: 'string', example: 'jokowi'),
        new OA\Property(property: 'email', type: 'string', example: 'jokowi@sipd.go.id'),
        new OA\Property(property: 'nip_nik', type: 'string', example: '1234567890'),
        new OA\Property(property: 'phone', type: 'string', example: '08123456789'),
        new OA\Property(
            property: 'roles',
            type: 'array',
            items: new OA\Items(type: 'string', example: 'applicant')
        ),
    ]
)]



class RegisterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'nip_nik' => $this->nip_nik,
            'phone' => $this->phone,
            'roles' => $this->getRoleNames()
        ];
    }
}
