<?php

namespace App\Http\Resources\Document\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "AdminDocumentSummaryResource",
    type:'object',
    properties:[
        new OA\Property(property : 'submitted', type: 'integer',example:1),
        new OA\Property(property : 'revision', type: 'integer',example:0),
        new OA\Property(property : 'approved', type: 'integer',example:2),
        new OA\Property(property : 'rejected', type: 'integer',example:1),
    ]
)]

class DocumentSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'submitted' => $this->resource['submitted'],
            'revision' => $this->resource['revision'],
            'approved' => $this->resource['approved'],
            'rejected' => $this->resource['rejected']
        ];
    }
}
