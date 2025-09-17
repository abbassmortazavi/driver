<?php

namespace App\Http\Controllers\Api\V1\Vehicle;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Vehicle\VehicleUpdateRequest;
use App\Http\Resources\Api\V1\Vehicle\VehicleResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Models\Vehicle;
use App\Repository\Vehicle\VehicleRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Put(
    path: '/api/v1/vehicles/{vehicle}',
    operationId: 'updateVehicle',
    summary: 'Update vehicle information',
    security: [['bearerAuth' => []]],
    tags: ['Vehicle'],
    parameters: [
        new OA\Parameter(
            name: 'vehicle',
            description: 'Vehicle ID',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'string'),
        ),
    ]
)]
#[OA\RequestBody(
    description: 'Vehicle update data',
    required: true,
    content: new OA\JsonContent(
        ref: VehicleUpdateRequest::class
    )
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Vehicle updated successfully',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                ref: VehicleResource::class,
                type: 'object',
            ),
            new OA\Property(
                property: 'success',
                type: 'boolean',
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
    description: 'Server error during update',
    content: new OA\JsonContent(
        ref: '#/components/schemas/ApiResponseErrorServer',
    )
)]
class VehicleUpdateController extends ApiController
{
    /**
     * @param  VehicleRepositoryInterface  $repository
     */
    public function __construct(protected VehicleRepositoryInterface $repository) {}

    /**
     * @param  VehicleUpdateRequest  $request
     * @param  Vehicle  $vehicle
     * @return JsonResponse
     */
    public function __invoke(VehicleUpdateRequest $request, Vehicle $vehicle): JsonResponse
    {
        Gate::authorize('update', $vehicle);

        return ApiResponse::ok(VehicleResource::make(
            $this->repository->update($vehicle->getKey(), $this->prepareData($request))
        ));
    }

    /**
     * @param  VehicleUpdateRequest  $request
     * @return array
     */
    public function prepareData(VehicleUpdateRequest $request): array
    {
        return [
            'name' => $request->input('name'),
            'vehicle_type_id' => $request->input('vehicle_type_id'),
            'fuel_type' => $request->input('fuel_type'),
            'plate_number' => $request->input('plate_number'),
            'vin_number' => $request->input('vin_number'),
            'insurance_policy_number' => $request->input('insurance_policy_number'),
            'insurance_expiry_date' => $request->input('insurance_expiry_date'),
            'color' => $request->input('color'),
            'model_name' => $request->input('model_name'),
            'brand_name' => $request->input('brand_name'),
            'year' => $request->input('year'),
        ];
    }
}
