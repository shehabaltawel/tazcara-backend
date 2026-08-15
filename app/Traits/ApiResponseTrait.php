<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * A trait class includes api response structure
 */
trait ApiResponseTrait
{
    /**
     * Generate successful json response
     */
    public function jsonSuccess(mixed $data, string $message = '', int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'error' => false,
            'message' => $message,
            'data' => $data,
        ], $code, [], JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES);
    }

    /**
     * * Generate failure json response
     */
    public function jsonError(?string $message, int $code = Response::HTTP_BAD_REQUEST, string $debug = ''): JsonResponse
    {
        $response = [
            'error' => true,
            'message' => $message,
            'data' => [],
        ];

        if (! app()->environment('production')) {
            $response['debug'] = $debug;
        }

        return response()->json($response, $code);
    }
}
