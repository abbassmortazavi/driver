<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\KycLog;
use App\Repository\Driver\DriverRepositoryInterface;
use App\Repository\KycLog\KycLogRepositoryInterface;
use App\Services\ThirdParties\Contracts\BiometricVerificationInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\KycLogStatusEnum;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Get(
    path: '/api/v1/drivers/check-verify-biometric/{trackId}',
    operationId: 'DriverCheckVerifyBiometric',
    summary: 'Driver Check Verify Biometric',
    security: [['bearerAuth' => []]],
    tags: ['Driver'],
    parameters: [
        new OA\Parameter(
            name: 'trackId',
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
                properties: [
                    new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'your biometric validation was successful'
                    ),
                ],
                type: 'object'
            ),
        ],
        type: 'object',
    )
)]
#[OA\Response(
    response: Response::HTTP_UNPROCESSABLE_ENTITY,
    description: 'Validation error',
    content: new OA\JsonContent(ref: '#/components/schemas/ApiResponseErrorValidation')
)]
#[OA\Response(
    response: Response::HTTP_INTERNAL_SERVER_ERROR,
    description: 'Server error during registration',
    content: new OA\JsonContent(ref: '#/components/schemas/ApiResponseErrorServer')
)]
class DriverCheckVerifyBiometricController extends ApiController
{
    public function __construct(
        private readonly BiometricVerificationInterface $biometricVerificationService,
        private readonly DriverRepositoryInterface $driverRepository,
        private readonly KycLogRepositoryInterface $kycLogRepository,
    ) {}

    /**
     * @param  string  $trackId
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function __invoke(string $trackId): JsonResponse
    {
        return DB::transaction(function () use ($trackId) {
            $kycLog = $this->findKycLogOrFail($trackId);
            if ($kycLog->status == KycLogStatusEnum::VERIFIED) {
                return $this->successResponse('messages.your_biometric_validation_was_successful');
            }
            if ($kycLog->status == KycLogStatusEnum::REJECTED) {
                return $this->successResponse('messages.your_biometric_validation_encountered_an_error');
            }
            $isVerified = $this->biometricVerificationService->checkVerify($trackId);
            if ($isVerified) {
                $this->driverRepository->update(auth()->user()->driver->id, [
                    'biometric_verified_at' => now(),
                ]);
            }
            $this->kycLogRepository->update($kycLog->id, [
                'status' => $isVerified ? KycLogStatusEnum::VERIFIED : KycLogStatusEnum::REJECTED,
            ]);

            return $this->successResponse(
                $isVerified
                    ? 'messages.your_biometric_validation_was_successful'
                    : 'messages.your_biometric_validation_encountered_an_error'
            );
        });
    }

    /**
     * @throws Exception
     */
    private function findKycLogOrFail(string $trackId): KycLog
    {
        $kycLog = $this->kycLogRepository->findByUserIdAndTrackId(auth()->id(), $trackId);
        if (! $kycLog) {
            throw ValidationException::withMessages([
                'kyc_log' => trans('messages.kyc_log_not_found'),
            ]);
        }

        return $kycLog;
    }

    private function successResponse(string $messageKey): JsonResponse
    {
        return response()->json([
            'message' => trans($messageKey),
        ]);
    }
}
