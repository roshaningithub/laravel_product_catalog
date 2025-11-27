<?php

use Illuminate\Http\JsonResponse;

function apiResponse(bool $status, int $code, ?string $message = null, $data = null): JsonResponse
{
    return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data,
    ], $code);
}
