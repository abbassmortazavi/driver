<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\User\PublicUserResource;
use App\Http\Responses\Api\V1\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/v1/me',
    operationId: 'me',
    summary: 'Retrieve Current User Data',
    security: [['bearerAuth' => []]],
    tags: ['Auth'],
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Successful response',
    content: new OA\JsonContent(
        ref: PublicUserResource::class
    )
)]
final class UserMeController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
    {
        return ApiResponse::ok(
            PublicUserResource::make(
                $request->user()->load('driver')
            )
        );
    }
}
