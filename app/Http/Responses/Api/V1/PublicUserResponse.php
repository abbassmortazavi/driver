<?php

namespace App\Http\Responses\Api\V1;

use App\Http\Resources\Api\V1\User\PublicUserResource;
use App\Models\PublicUser;
use OpenApi\Attributes as OA;

class PublicUserResponse
{
    #[OA\Schema(
        schema: 'PublicUserResponseMe',
        properties: [
            new OA\Property(
                property: 'data',
                ref: PublicUserResource::class,
                type: 'object',
            ),
        ],
    )]
    public static function me(PublicUser $user): \Illuminate\Http\JsonResponse
    {
        return ApiResponse::ok(
            PublicUserResource::make($user)
        );
    }

    #[OA\Schema(
        schema: 'PublicUserResponseShow',
        properties: [
            new OA\Property(
                property: 'data',
                ref: PublicUserResource::class,
                type: 'object',
            ),
        ],
    )]
    public static function show(PublicUser $user): \Illuminate\Http\JsonResponse
    {
        return ApiResponse::ok(
            PublicUserResource::make($user)
        );
    }

    public static function delete(PublicUser $user): \Illuminate\Http\JsonResponse
    {
        return ApiResponse::noContent();
    }
}
