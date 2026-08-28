<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class JsonSchemaRef extends OA\JsonContent
{
    public function __construct(string $schemaName)
    {
        parent::__construct(
            ref: "#/components/schemas/{$schemaName}"
        );
    }
}
