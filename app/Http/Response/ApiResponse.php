<?php

namespace App\Http\Response;

class ApiResponse
{
    public static function success($data = null, string $message = 'success', int $status = 200)
    {
        return response()->json(
            [
                'meta' => [
                    'message' => $message,
                ],
                'data' => $data,
            ], $status
        );
    }

    public static function error($data = null, string $message = 'error', int $status = 400)
    {
        return response()->json([
            'meta' => [
                'message' => $message,
            ],
            'data' => $data,
        ], $status);
    }
}
