<?php

namespace App\Http\Controllers\Api\V1\VehicleType;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\Vehicle\VehicleTypeResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Repository\VehicleType\VehicleTypeRepositoryInterface;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Get(
    path: '/api/v1/vehicle-types',
    operationId: 'VehicleType',
    summary: 'List all vehicle types',
    tags: ['General'],
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Successful response',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                ref: VehicleTypeResource::class,
                type: 'object',
            ),
        ],
    )
)]
#[OA\Response(
    response: Response::HTTP_INTERNAL_SERVER_ERROR,
    description: 'Server error during vehicle types',
    content: new OA\JsonContent(
        ref: '#/components/schemas/ApiResponseErrorServer',
    )
)]
class VehicleTypeListController extends ApiController
{
    /**
     * @param  VehicleTypeRepositoryInterface  $vehicleTypeRepository
     */
    public function __construct(private readonly VehicleTypeRepositoryInterface $vehicleTypeRepository) {}

    /**
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        return ApiResponse::ok(VehicleTypeResource::collection($this->vehicleTypeRepository->all()));
    }
}
