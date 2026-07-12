<?php

namespace Purdia\Shared\Support;

use Illuminate\Http\JsonResponse;

final class ApiResponse
{
    public static function success(mixed $data = null, string $message = null, int $status = 200): JsonResponse
    {
        $response = [];

        if ($message !== null) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    public static function created(mixed $data = null, string $message = 'Resource created successfully.'): JsonResponse
    {
        return static::success($data, $message, 201);
    }

    public static function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }
}
