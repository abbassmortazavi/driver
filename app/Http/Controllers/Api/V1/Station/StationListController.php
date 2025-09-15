<?php

namespace App\Http\Controllers\Api\V1\Station;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\StationResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Repository\Station\StationRepositoryInterface;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Get(
    path: '/api/v1/stations',
    operationId: 'Station',
    summary: 'List all stations',
    tags: ['General'],
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Successful response',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                ref: StationResource::class,
                type: 'object',
            ),
        ],
    )
)]
#[OA\Response(
    response: Response::HTTP_INTERNAL_SERVER_ERROR,
    description: 'Server error during stations',
    content: new OA\JsonContent(
        ref: '#/components/schemas/ApiResponseErrorServer',
    )
)]
class StationListController extends ApiController
{
    /**
     * @param  StationRepositoryInterface  $stationRepository
     */
    public function __construct(private readonly StationRepositoryInterface $stationRepository) {}

    /**
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        return ApiResponse::ok(StationResource::collection($this->stationRepository->all()));
    }
}
