<?php

namespace App\Http\Responses\Api\V1;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ApiResponse
{
    public static function ok(mixed $data, ?iterable $meta = null): JsonResponse
    {
        return static::success($data, $meta, SymfonyResponse::HTTP_OK);
    }

    public static function created(mixed $data, ?iterable $meta = null): JsonResponse
    {
        return static::success($data, $meta, SymfonyResponse::HTTP_CREATED);
    }

    public static function noContent(): JsonResponse
    {
        return static::success(null, null, SymfonyResponse::HTTP_NO_CONTENT);
    }

    public static function accepted(mixed $data, ?iterable $meta = null): JsonResponse
    {
        return static::success($data, $meta, SymfonyResponse::HTTP_ACCEPTED);
    }

    public static function success(mixed $data, mixed $meta, int $status): JsonResponse
    {
        $response = null;

        if (! empty($data)) {
            $response['data'] = $data;
        }

        if (! empty($meta)) {
            $response['meta'] = $meta;
        }
        $response['success'] = true;

        return response()->json($response, $status);
    }

    #[OA\Schema(
        schema: 'ApiResponseErrorUnauthorized',
        properties: [
            new OA\Property(
                property: 'success',
                type: 'boolean',
                example: false
            ),
            new OA\Property(
                property: 'message',
                type: 'string',
                example: 'Server error message'
            ),
            new OA\Property(
                property: 'errors',
                description: 'Validation errors',
                type: 'object',
                additionalProperties: new OA\AdditionalProperties(
                    type: 'array',
                    items: new OA\Items(type: 'string')
                )
            ),
        ]
    )]
    public static function unauthorized(string $message): JsonResponse
    {
        return static::error($message, [], SymfonyResponse::HTTP_UNAUTHORIZED);
    }

    #[OA\Schema(
        schema: 'ApiResponseErrorValidation',
        properties: [
            new OA\Property(
                property: 'success',
                type: 'boolean',
                example: false
            ),
            new OA\Property(
                property: 'message',
                type: 'string',
                example: 'Server error message'
            ),
            new OA\Property(
                property: 'errors',
                description: 'Validation errors',
                type: 'object',
                additionalProperties: new OA\AdditionalProperties(
                    type: 'array',
                    items: new OA\Items(type: 'string')
                )
            ),
        ]
    )]
    public static function validation(string $message): JsonResponse
    {
        return static::error($message, [], SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function unprocessableEntity(string $message): JsonResponse
    {
        return static::validation($message);
    }

    public static function forbidden(string $message): JsonResponse
    {
        return static::error($message, [], SymfonyResponse::HTTP_FORBIDDEN);
    }

    #[OA\Schema(
        schema: 'ApiResponseErrorServer',
        properties: [
            new OA\Property(
                property: 'success',
                type: 'boolean',
                example: false
            ),
            new OA\Property(
                property: 'message',
                type: 'string',
                example: 'Server error message'
            ),
        ]
    )]
    public static function server(string $message, $status = null): JsonResponse
    {
        return static::error($message, [], $status ?? SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function error(string $message, array $errors, int $status): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'success' => false,
            'errors' => $errors,
            'status' => $status,
        ], $status);
    }

    public static function notFound(string $message): JsonResponse
    {
        return static::error($message, [], SymfonyResponse::HTTP_NOT_FOUND);
    }
}
