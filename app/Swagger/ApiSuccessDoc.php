<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class ApiSuccessDoc extends OA\Response
{
    public function __construct(
        int $status = 200,
        string $message = 'success',
        ?JsonSchemaRef $data = null
    ) {
        $dataContent = $data !== null ? new OA\Property(property : 'data', ref: $data->schemaRef)
        : new OA\Property(property: 'data', type: 'object', nullable:true);

        parent::__construct(
            response: $status,
            description: 'Response Success',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'meta',
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'status', type: 'integer', example: $status),
                            new OA\Property(property: 'message', type: 'string', example: $message),
                        ]
                    ),
                    $dataContent,
                ]
            )
        );
    }
}
