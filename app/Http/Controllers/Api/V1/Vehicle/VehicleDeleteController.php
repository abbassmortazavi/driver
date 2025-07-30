<?php

namespace App\Http\Controllers\Api\V1\Vehicle;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Models\Vehicle;
use App\Repository\Vehicle\VehicleRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\VehicleStatusEnum;
use Symfony\Component\HttpFoundation\Response;

#[OA\Delete(
    path: '/api/v1/vehicles/{vehicle}',
    operationId: 'deleteVehicleStatus',
    summary: 'Delete vehicle status',
    security: [['bearerAuth' => []]],
    tags: ['Vehicle'],
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
class VehicleDeleteController extends ApiController
{
    /**
     * @param  VehicleRepositoryInterface  $vehicleRepository
     */
    public function __construct(protected VehicleRepositoryInterface $vehicleRepository) {}

    /**
     * @param  Vehicle  $vehicle
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function __invoke(Vehicle $vehicle): JsonResponse
    {
        $driver = auth()->user()->driver;
        $driverVehicleAssign = $this->vehicleRepository->getDriverVehicle($driver->id, $vehicle->id);
        if (is_null($driverVehicleAssign)) {
            throw ValidationException::withMessages([
                __('Driver Is Not Assign To Vehicle'),
            ]);
        }
        if ($vehicle->status == VehicleStatusEnum::ACTIVE->value) {
            throw ValidationException::withMessages([
                __('Vehicle Is Active Can Not To Delete'),
            ]);
        }
        $vehicle->delete();

        return ApiResponse::ok([
            'message' => __('Driver Vehicle Deleted Successfully'),
        ]);
    }
}
