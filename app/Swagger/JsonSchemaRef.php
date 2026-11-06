<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class JsonSchemaRef extends OA\MediaType
{
    public string $schemaRef;

    public function __construct(string $schemaName, string $mediaType = 'application/json')
    {
        $this->schemaRef = "#/components/schemas/{$schemaName}";
        parent::__construct(
            mediaType : $mediaType,
            schema: new OA\Schema(ref: $this->schemaRef) ,
        );
    }
}
