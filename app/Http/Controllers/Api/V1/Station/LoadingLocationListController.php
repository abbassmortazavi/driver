<?php

namespace App\Http\Controllers\Api\V1\Station;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\LoadingLocationResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Models\Station;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Get(
    path: '/api/v1/stations/{station}/loading-locations',
    operationId: 'LoadingLocation',
    summary: 'List all loading locations for a station',
    tags: ['General'],
    parameters: [
        new OA\Parameter(
            name: 'station',
            description: 'Station ID',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'string')
        ),
    ]
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Successful response',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                ref: LoadingLocationResource::class,
                type: 'object',
            ),
        ],
    )
)]

#[OA\Response(
    response: Response::HTTP_INTERNAL_SERVER_ERROR,
    description: 'Server error during loadingLocations',
    content: new OA\JsonContent(
        ref: '#/components/schemas/ApiResponseErrorServer',
    )
)]
class LoadingLocationListController extends ApiController
{
    public function __invoke(Station $station): JsonResponse
    {
        //check me
        return ApiResponse::ok(LoadingLocationResource::collection($station->loadingLocations));
    }
}
