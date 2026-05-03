<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function successResponse($data, string $message = null, int $code = 200, array $meta = null): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if ($meta) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    protected function createdResponse($data, string $message = 'Resource created successfully'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], 201);
    }

    protected function errorResponse(string $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $message,
        ], $code);
    }

    protected function deletedResponse(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Resource deleted successfully',
        ], 200);
    }
}
