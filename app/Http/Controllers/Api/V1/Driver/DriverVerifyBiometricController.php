<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Driver\VerifyDriverBiometricRequest;
use App\Repository\KycLog\KycLogRepositoryInterface;
use App\Services\ThirdParties\Contracts\BiometricVerificationInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\KycLogStatusEnum;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Post(
    path: '/api/v1/drivers/verify-biometric',
    operationId: 'Driver Verify Biometric',
    summary: 'Driver Verify Biometric',
    security: [['bearerAuth' => []]],
    tags: ['Driver'],
)]
#[OA\RequestBody(
    description: 'Driver Verify Biometric data',
    required: true,
    content: new OA\JsonContent(
        ref: VerifyDriverBiometricRequest::class
    )
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
class DriverVerifyBiometricController extends ApiController
{
    public function __construct(private readonly BiometricVerificationInterface $biometricVerificationService,
        private readonly KycLogRepositoryInterface $kycLogRepository) {}

    /**
     * @throws Throwable
     */
    public function __invoke(VerifyDriverBiometricRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $this->hasPendingVerification();
            //            $videoPath = $request->file('video')->store('videos', 'public');
            //            $videoFullPath = Storage::disk('public')->path($videoPath);
            $trackId = $this->biometricVerificationService->verify(auth()->id(), $request->input('video'));
            //            Storage::disk('public')->delete($videoPath);

            return response()->json([
                'message' => trans('messages.processing_video_response_will_be_ready_within_the_next_10_minutes'),
                'track_id' => $trackId,
            ]);
        });
    }

    /**
     * @throws Exception
     */
    private function hasPendingVerification(): void
    {
        $kycLog = $this->kycLogRepository->findByUserIdAndStatus(auth()->id(), KycLogStatusEnum::PENDING);
        if ($kycLog) {
            throw ValidationException::withMessages([
                'biometric_verification' => trans('messages.you_have_a_pending_biometric_verification_please_wait')."'track_id:'$kycLog->track_id",
            ]);
        }
    }
}
