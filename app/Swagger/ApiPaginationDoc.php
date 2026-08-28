<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class ApiPaginationDoc extends OA\Response
{
    public function __construct(
        int $status = 200,
        string $message = 'pagination',
        ?array $data = null
    ) {
        $dataContent = $data !== null ? new OA\Items(type: 'object', properties: $data)
            : new OA\Items(type: 'object');

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
                    new OA\Property(
                        property: 'data',
                        type: 'array',
                        items: $dataContent
                    ),
                    new OA\Property(
                        property: 'pagination',
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'total', type: 'integer', example: 20),
                            new OA\Property(property: 'per_page', type: 'integer', example: 5),
                            new OA\Property(property: 'current_page', type: 'integer', example: 1),
                            new OA\Property(property: 'last_page', type: 'integer', example: 4),
                        ]
                    ),
                ]
            )
        );
    }
}
