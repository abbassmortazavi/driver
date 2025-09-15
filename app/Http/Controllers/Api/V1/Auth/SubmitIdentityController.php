<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Driver\CreateDriverRequest;
use App\Http\Resources\Api\V1\Driver\DriverResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Models\PublicUser;
use App\Repository\Driver\DriverRepositoryInterface;
use App\Services\ThirdParties\Contracts\PhoneNumberVerificationInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\DriverStatusEnum;
use Patoughi\Common\Utilities\Text\TokenGenerator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Post(
    path: '/api/v1/auth/submit-identity',
    operationId: 'submit identity',
    summary: 'submit identity',
    security: [['bearerAuth' => []]],
    tags: ['Auth'],
)]
#[OA\RequestBody(
    description: 'Driver Submit Identity',
    required: true,
    content: new OA\JsonContent(
        ref: CreateDriverRequest::class
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
class SubmitIdentityController extends ApiController
{
    /**
     * @param  PhoneNumberVerificationInterface  $phoneNumberVerificationService
     * @param  DriverRepositoryInterface  $driverRepository
     */
    public function __construct(
        private readonly PhoneNumberVerificationInterface $phoneNumberVerificationService,
        private readonly DriverRepositoryInterface $driverRepository,
    ) {}

    /**
     * @throws Throwable
     */
    public function __invoke(CreateDriverRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            /** @var PublicUser $user */
            $user = auth()->user();
            $payload = $this->prepareData($request);
            $this->checkExistVerifiedDriver($user);
            $this->verifyPhoneNumber($user, $payload['national_code']);
            $driver = $this->driverRepository->updateOrCreate([
                'user_id' => $user->getKey(),
            ], $payload);

            return ApiResponse::ok([DriverResource::make($driver)]);
        });
    }

    /**
     * @throws Exception
     */
    private function checkExistVerifiedDriver(PublicUser $user): void
    {
        if ($user->driver && $user->driver->identity_verified_at) {
            throw ValidationException::withMessages([
                'driver' => trans('messages.driver_already_exist'),
            ]);
        }
    }

    /**
     * @throws Exception
     */
    private function verifyPhoneNumber(PublicUser $user, string $nationalCode): void
    {
        if (! $this->phoneNumberVerificationService->verify($user->id, $user->phone_number, $nationalCode)) {
            throw ValidationException::withMessages([
                'national_code' => trans('messages.national_code_and_mobile_number_do_not_match'),
            ]);
        }
    }

    /**
     * @param  CreateDriverRequest  $request
     * @return array
     */
    private function prepareData(CreateDriverRequest $request): array
    {
        return [
            'mdm_organization_id' => TokenGenerator::generateUniqueNumber('drivers', 'mdm_organization_id'),
            'user_id' => auth()->id(),
            'status' => DriverStatusEnum::ACTIVE->value,
            'first_name' => $request->input('first_name') ?? null,
            'last_name' => $request->input('last_name') ?? null,
            'national_code' => $request->input('national_code') ?? null,
            'gender' => $request->input('gender') ?? null,
            'birth_date' => $request->input('birth_date') ?? null,
            'hired_at' => Carbon::now(),
            'activated_at' => Carbon::now(),
            'identity_verified_at' => Carbon::now(),
        ];
    }
}
