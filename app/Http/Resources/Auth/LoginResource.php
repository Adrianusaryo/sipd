<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LoginResource',
    type: 'object',
    properties: [
        new OA\Property(
            property: 'user',
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'Joko Widodo'),
                new OA\Property(property: 'email', type: 'string', example: 'jokowi@sipd.go.id'),
                new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string', example: 'applicant')),

            ]
        ),
        new OA\Property(
            property: 'permissions',
            type: 'array',
            items: new OA\Items(type: 'string', example: 'documents.create')
        ),
        new OA\Property(property: 'access_token', type: 'string', example: '1|laravel_sanctum_token_hash_here'),
    ]
)]

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this->resource['user']->id,
                'name' => $this->resource['user']->name,
                'email' => $this->resource['user']->email,
                'roles' => $this->resource['user']->getRoleNames(),
            ],
            'permissions' => $this->resource['permissions'],
            'access_token' => $this->resource['token'],
        ];
    }
}
