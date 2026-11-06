<?php

namespace App\Http\Resources\Document\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "AdminDocumentUpdateResource",
    type:'object',
    properties:[
        new OA\Property(property : 'id', type: 'integer',example:1),
        new OA\Property(property : 'status', type: 'string',example: 'Approved'),
        new OA\Property(property : 'admin_notes', type: 'string',example: 'Dokumen sudah lengkap dan disetujui.'),
    ]
)]

class DocumentUpdateResource extends JsonResource
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
            'status' => $this->resource['status'],
            'admin_notes' => $this->resource['admin_notes']
        ];
    }
}
