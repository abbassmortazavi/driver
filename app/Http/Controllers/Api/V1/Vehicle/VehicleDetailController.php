<?php

namespace App\Http\Controllers\Api\V1\Vehicle;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\Vehicle\VehicleResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Get(
    path: '/api/v1/vehicles/{vehicle}',
    operationId: 'vehicleDetail',
    summary: 'Vehicle detail',
    security: [['bearerAuth' => []]],
    tags: ['Vehicles'],
    parameters: [
        new OA\Parameter(
            name: 'vehicle',
            description: 'Vehicle ID',
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
                ref: VehicleResource::class,
                type: 'object',
            ),
        ],
    )
)]
#[OA\Response(
    response: Response::HTTP_UNPROCESSABLE_ENTITY,
    description: 'Validation error',
    content: new OA\JsonContent(
        ref: '#/components/schemas/ApiResponseErrorValidation',
    )
)]
#[OA\Response(
    response: Response::HTTP_INTERNAL_SERVER_ERROR,
    description: 'Server error during registration',
    content: new OA\JsonContent(
        ref: '#/components/schemas/ApiResponseErrorServer',
    )
)]
class VehicleDetailController extends ApiController
{
    /**
     * @param  Vehicle  $vehicle
     * @return JsonResponse
     */
    public function __invoke(Vehicle $vehicle)
    {
        Gate::authorize('view', $vehicle);

        return ApiResponse::ok(VehicleResource::make($vehicle));
    }
}
