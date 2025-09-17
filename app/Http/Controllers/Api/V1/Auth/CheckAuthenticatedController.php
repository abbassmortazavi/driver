<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\Auth\UserAuthenticatedResource;
use App\Http\Responses\Api\V1\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/v1/auth/check-authenticated',
    operationId: 'checkAuthenticated',
    summary: 'Check if User is Authenticated',
    security: [['bearerAuth' => []]],
    tags: ['Auth'],
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Successful response',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                ref: UserAuthenticatedResource::class,
                type: 'object'
            ),
        ])
)]
#[OA\Response(
    response: Response::HTTP_UNAUTHORIZED,
    description: 'Unauthorized',
    content: new OA\JsonContent(
        ref: '#/components/schemas/ApiResponseErrorUnauthorized'
    )
)]
final class CheckAuthenticatedController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
    {
        return ApiResponse::ok(
            data: UserAuthenticatedResource::make([
                'is_authenticated' => auth('api')->check(),
            ])
        );
    }
}
