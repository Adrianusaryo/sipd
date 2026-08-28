<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class ApiSuccessDoc extends OA\Response
{
    public function __construct(
        int $status = 200,
        string $message = 'success',
        ?array $data = null
    ) {
        $dataProperty = $data !== null ? new OA\Property(property: 'data', type: 'object', properties: $data) :
        new OA\Property(property: 'data', type: 'object', nullable: true, example: null);

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
                    $dataProperty,
                ]
            )
        );
    }
}
