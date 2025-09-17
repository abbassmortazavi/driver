<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\User\PublicUserResource;
use App\Http\Responses\Api\V1\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

#[OA\Get(
    path: '/api/v1/me',
    operationId: 'me',
    summary: 'Retrieve Current User Data',
    security: [['bearerAuth' => []]],
    tags: ['Auth'],
)]
#[OA\Response(
    response: ResponseAlias::HTTP_OK,
    description: 'Successful response',
    content: new OA\JsonContent(
        ref: PublicUserResource::class
    )
)]
final class UserMeController extends ApiController
{
    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        return ApiResponse::ok(
            PublicUserResource::make(
                $request->user()->load('driver')
            )
        );
    }
}
