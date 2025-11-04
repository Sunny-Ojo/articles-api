<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    public function success(
        $data,
        $message = 'Operation Successful',
        $statusCode = Response::HTTP_OK,
    ): JsonResponse {

        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        // Handle pagination meta and links if it's a paginated resource collection
        if ($data instanceof AnonymousResourceCollection) {
            $paginationData = $data->response()->getData(true);
            $response['meta'] = $paginationData['meta'] ?? null;
            $response['links'] = $paginationData['links'] ?? null;
        }

        return response()->json($response, $statusCode);
    }
    public static function error(string $message = 'Error', int $status = Response::HTTP_BAD_REQUEST, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
