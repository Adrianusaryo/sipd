<?php

namespace App\Http\Resources\Document\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "AdminDocumentResource",
    type:'object',
    properties:[
        new OA\Property(property : 'id', type: 'integer',example:1),
        new OA\Property(property : 'number_registration', type: 'string',example: 'REG-20261102-XT0CH'),
        new OA\Property(property : 'username', type: 'string',example: 'Elizabeth Angel'),
        new OA\Property(property : 'title', type: 'string',example: 'Pengajuan Dokumen Elizabeth'),
        new OA\Property(property : 'status', type: 'string',example: 'submitted'),
    ]
)]

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'number_registration' => $this->resource['number_registration'],
            'username' => $this->resource['user']['username'],
            'title' => $this->resource['title'],
            'status' => $this->resource['status']
        ];
    }
}
