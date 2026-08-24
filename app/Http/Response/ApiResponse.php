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

    public static function pagination(array $result, string $message = 'pagination', int $status = 200)
    {
        return response()->json([
            'meta' => [
                'message' => $message,
            ],
            'data' => $result['items'],
            'pagination' => [
                'total' => $result['total'],
                'per_page' => $result['perPage'],
                'current_page' => $result['currentPage'],
                'last_page' => $result['lastPage'],
            ],
        ], $status);
    }
}
