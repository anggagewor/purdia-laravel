<?php

namespace Purdia\Shared\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiExceptionRenderer
{
    public function render(Request $request, DomainException $exception): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => $exception->errorCode,
                'message' => $exception->getMessage(),
                'context' => $exception->context ?: null,
            ],
        ], $exception->httpStatus);
    }

    public function shouldRender(Request $request, \Throwable $exception): bool
    {
        return $exception instanceof DomainException && $request->is('api/*');
    }
}
