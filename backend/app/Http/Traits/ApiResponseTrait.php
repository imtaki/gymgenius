<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function successResponse($data, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'error' => null,
            'meta' => null,
        ], $code);
    }

    protected function createdResponse($data, string $message = 'Resource created successfully'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'error' => null,
            'meta' => null,
            'message' => $message,
        ], 201);
    }

    protected function errorResponse(string $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'error' => $message,
            'meta' => null,
        ], $code);
    }

    protected function deletedResponse(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => null,
            'error' => null,
            'meta' => null,
            'message' => 'Resource deleted successfully',
        ], 200);
    }
}
