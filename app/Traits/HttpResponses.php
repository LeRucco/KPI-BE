<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Sanctum\NewAccessToken;

trait HttpResponses
{
    protected function success(?array $data, int $code = 200, ?string $message = null): JsonResponse
    {
        // $data['status'] = 'Request was successfull';
        // $data['message'] = $message;
        // return response()->json($data, $code);
        return response()->json([
            'status' => 'Request was successfully',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function successAuth(array $data, NewAccessToken $token, int $code = 200,  ?string $message = 'Authenticated User'): JsonResponse
    {
        return response()->json([
            'status' => 'Request was successfully',
            'message' => $message,
            'token' => $token->plainTextToken,
            'data' => $data
        ], $code);
    }

    protected function errorAuthenticate(int $code = 401, ?string $message = ''): JsonResponse
    {
        return response()->json([
            'status' => 'Error has occured',
            'message' => $message
        ], $code);
    }

    protected function successPaginate(array $data, int $code = 200, ?string $message = null): JsonResponse
    {
        return response()->json([
            'status' => 'Request paginated was successfull',
            'message' => $message,
            'data' => $data['data'],
            'links' => $data['links'],
            'meta' => $data['meta'],
        ], $code);
    }

    protected function error(?array $data, int $code, ?string $message = null): JsonResponse
    {
        return response()->json([
            'status' => 'Error has occured',
            'message' => $message,
            'errors' => $data
        ], $code);
    }

    protected function notFound(string $message = null, int $code = 404): JsonResponse
    {
        return response()->json([
            'status' => 'NotFoundHttpException',
            'message' => $message,
        ], $code);
    }

    protected function notSupportedMethod(string $message = null, int $code = 405): JsonResponse
    {
        return response()->json([
            'status' => 'MethodNotAllowedHttpException',
            'message' => $message,
        ], $code);
    }

    protected function errorValidation(array $errors, int $code = Response::HTTP_UNPROCESSABLE_ENTITY, ?string $message): JsonResponse
    {
        return response()->json([
            'status' => "ValidationException",
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    protected function noAuthority(int $code = Response::HTTP_FORBIDDEN, string $message = 'No Authority'): JsonResponse
    {
        return response()->json([
            'status' => "NoAuthority",
            'message' => $message,
        ], $code);
    }
}
