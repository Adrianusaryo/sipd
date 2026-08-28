<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class ApiErrorDoc extends OA\Response
{
    public function __construct(int $status = 400, string $message = 'error', mixed $data = null)
    {
        parent::__construct(
            response: $status,
            description: 'Response Failed',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'meta',
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'message', type: 'string', example: $message),
                        ]
                    ),
                    new OA\Property(property: 'data', type: 'object', nullable: true, example: $data),
                ]
            )
        );
    }
}
