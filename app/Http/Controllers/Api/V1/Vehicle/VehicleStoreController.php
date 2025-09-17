<?php

namespace App\Http\Controllers\Api\V1\Vehicle;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Vehicle\CreateVehicleRequest;
use App\Http\Resources\Api\V1\Driver\DriverVehicleResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Models\Driver;
use App\Models\PublicUser;
use App\Repository\DriverVehicleAssignmentHistory\DriverVehicleAssignmentHistoryRepositoryInterface;
use App\Repository\Vehicle\VehicleRepositoryInterface;
use App\Services\ThirdParties\Contracts\VehicleVerificationInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\DriverStatusEnum;
use Patoughi\Common\Enums\DriverVehicleAssignmentHistoryStatusEnum;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Post(
    path: '/api/v1/vehicles',
    operationId: 'createVehicle',
    summary: 'Create Vehicle',
    security: [['bearerAuth' => []]],
    tags: ['Vehicle'],
)]
#[OA\RequestBody(
    description: 'Vehicle Store data',
    required: true,
    content: new OA\JsonContent(
        ref: CreateVehicleRequest::class
    )
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Successful response',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                ref: DriverVehicleResource::class,
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
class VehicleStoreController extends ApiController
{
    /**
     * @param  VehicleVerificationInterface  $vehicleVerificationService
     * @param  VehicleRepositoryInterface  $vehicleRepository
     * @param  DriverVehicleAssignmentHistoryRepositoryInterface  $driverVehicleAssignmentHistoryRepository
     */
    public function __construct(private readonly VehicleVerificationInterface $vehicleVerificationService,
        private readonly VehicleRepositoryInterface $vehicleRepository,
        private readonly DriverVehicleAssignmentHistoryRepositoryInterface $driverVehicleAssignmentHistoryRepository,
    ) {}

    /**
     * @param  CreateVehicleRequest  $request
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function __invoke(CreateVehicleRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $user = auth()->user(); /** @var PublicUser $user */
            $driver = auth()->user()->driver; /** @var Driver $driver */
            $payload = $this->prepareData($driver, $request);
            $this->verifyVehicle($user->id, $driver->national_code, $payload['plate_number']);
            $vehicle = $this->vehicleRepository->create($payload);
            $this->createDriverVehicleAssignmentHistory($driver->getKey(), $vehicle->getKey());

            return ApiResponse::ok([DriverVehicleResource::make($vehicle)]);
        });
    }

    /**
     * @param  Driver  $driver
     * @param  CreateVehicleRequest  $request
     * @return array
     */
    public function prepareData(Driver $driver, CreateVehicleRequest $request): array
    {
        return [
            'name' => $request->input('name'),
            'vehicle_type_id' => $request->input('vehicle_type_id'),
            'driver_id' => $driver->getKey(),
            'status' => DriverStatusEnum::ACTIVE,
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

    /**
     * @param  int  $userId
     * @param  string  $nationalCode
     * @param  string  $plateNumber
     * @return void
     *
     * @throws ValidationException
     */
    private function verifyVehicle(int $userId, string $nationalCode, string $plateNumber): void
    {
        if (! $this->vehicleVerificationService->verify($userId, $nationalCode, $plateNumber)) {
            throw ValidationException::withMessages([
                'national_code' => trans('messages.national_code_and_plate_number_do_not_match'),
            ]);
        }
    }

    /**
     * @param  int  $driverId
     * @param  int  $vehicleId
     * @return void
     */
    private function createDriverVehicleAssignmentHistory(int $driverId, int $vehicleId): void
    {
        $this->driverVehicleAssignmentHistoryRepository->create([
            'driver_id' => $driverId,
            'vehicle_id' => $vehicleId,
            'status' => DriverVehicleAssignmentHistoryStatusEnum::ACTIVE,
        ]);
    }
}
