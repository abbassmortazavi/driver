<?php

namespace App\Http\Controllers\Api\V1\Vehicle;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Vehicle\VehicleListRequest;
use App\Http\Resources\Api\V1\Vehicle\VehicleResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Models\Vehicle;
use App\Repository\Driver\DriverRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Get(
    path: '/api/v1/vehicles',
    operationId: 'vehicle lists',
    summary: 'vehicle lists',
    security: [['bearerAuth' => []]],
    tags: ['Vehicle'],
    parameters: [
        new OA\Parameter(
            name: 'status',
            description: 'Status',
            in: 'query',
            required: false,
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
class VehicleListController extends ApiController
{
    /**
     * @param  DriverRepositoryInterface  $driverRepository
     */
    public function __construct(protected DriverRepositoryInterface $driverRepository) {}

    /**
     * @param  VehicleListRequest  $request
     * @return JsonResponse
     */
    public function __invoke(VehicleListRequest $request): JsonResponse
    {
        $driver = auth()->user()->driver;
        Gate::authorize('owner', $driver);
        $vehicles = $driver->vehicles()
            ->when(isset($attributes['status']), fn ($q) => $q->where('status', $request->get('status')))
            ->get();

        return ApiResponse::ok(VehicleResource::collection($vehicles));
    }
}
