<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Driver\UpdateDriverRequest;
use App\Http\Resources\Api\V1\Driver\DriverResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Models\Driver;
use App\Models\PublicUser;
use App\Repository\Driver\DriverRepositoryInterface;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Put(
    path: '/api/v1/auth/submit-information',
    operationId: 'submitInformation',
    summary: 'Driver Submit Information',
    security: [['bearerAuth' => []]],
    tags: ['Auth'],
)]
#[OA\RequestBody(
    description: 'Driver Update data',
    required: true,
    content: new OA\JsonContent(
        ref: UpdateDriverRequest::class
    )
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Successful response',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                ref: DriverResource::class,
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
class UpdateIdentityController extends ApiController
{
    /**
     * @param  DriverRepositoryInterface  $driverRepository
     */
    public function __construct(private readonly DriverRepositoryInterface $driverRepository) {}

    /**
     * @param  UpdateDriverRequest  $request
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function __invoke(UpdateDriverRequest $request): JsonResponse
    {
        /** @var PublicUser $user */
        $user = auth()->user();
        $driver = $user->driver;
        $driver = $this->driverRepository->update($driver->id, $this->prepareData($driver, $request));

        return ApiResponse::ok([DriverResource::make(
            $this->driverRepository->update($driver->id, $this->prepareData($driver, $request))
        )]);
    }

    /**
     * @param  Driver  $driver
     * @param  UpdateDriverRequest  $request
     * @return array
     */
    private function prepareData(Driver $driver, UpdateDriverRequest $request): array
    {
        return [
            'licence_number' => $request->input('licence_number') ?? $driver->licence_number,
            'licence_type' => $request->input('licence_type') ?? $driver->licence_type,
            'licence_expired_at' => $request->input('licence_expired_at') ?? $driver->licence_expired_at,
            'emergency_contact_name' => $request->input('emergency_contact_name') ?? $driver->emergency_contact_name,
            'emergency_contact_phone' => $request->input('emergency_contact_phone') ?? $driver->emergency_contact_phone,
        ];
    }
}
