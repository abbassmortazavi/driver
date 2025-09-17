<?php

namespace App\Http\Controllers\Api\V1\Vehicle;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Vehicle\VehicleStatusUpdateRequest;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Models\Vehicle;
use App\Repository\Vehicle\VehicleRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Patch(
    path: '/api/v1/vehicles/{vehicle}/change-status',
    operationId: 'updateVehicleChangeStatus',
    summary: 'Update vehicle Change status',
    security: [['bearerAuth' => []]],
    tags: ['Vehicle'],
    parameters: [
        new OA\Parameter(
            name: 'vehicle',
            in: 'path',
            description: 'Vehicle ID',
            required: true,
            schema: new OA\Schema(type: 'string')
        ),
    ]
)]
#[OA\RequestBody(
    description: 'Update vehicle Change Status',
    required: true,
    content: new OA\JsonContent(
        ref: VehicleStatusUpdateRequest::class
    )
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Successful response',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                properties: [
                    new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Vehicle Updated Successfully'
                    ),
                ],
                type: 'object',
            ),
            new OA\Property(
                property: 'success',
                type: 'string',
                example: true
            ),
        ],
        type: 'object',
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
class VehicleChangeStatusController extends ApiController
{
    /**
     * @param  VehicleRepositoryInterface  $vehicleRepository
     */
    public function __construct(protected VehicleRepositoryInterface $vehicleRepository) {}

    /**
     * @param  VehicleStatusUpdateRequest  $request
     * @param  Vehicle  $vehicle
     * @return JsonResponse
     */
    public function __invoke(VehicleStatusUpdateRequest $request, Vehicle $vehicle): JsonResponse
    {
        Gate::authorize('driverChangeStatus', $vehicle);
        $this->vehicleRepository->update($vehicle->id, [
            'status' => $request->get('status'),
        ]);

        return ApiResponse::ok([
            'message' => __('Driver Vehicle Updated Successfully'),
        ]);
    }
}
