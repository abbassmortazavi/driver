<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Resources\Api\V1\Auth\OtpCodeResource;
use App\Http\Responses\Api\V1\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\OtpTypeEnum;
use Patoughi\Common\Enums\OtpViaEnum;
use Patoughi\Common\Services\Otp\OtpAlreadySentException;
use Patoughi\Common\Services\Otp\OtpServiceInterface;
use Symfony\Component\HttpFoundation\Response;

#[OA\Post(
    path: '/api/v1/auth/send-otp',
    operationId: 'sendOtpCode',
    summary: 'Send OTP Code',
    tags: ['Auth'],
)]
#[OA\RequestBody(
    description: 'Shipment Store data',
    required: true,
    content: new OA\JsonContent(
        ref: SendOtpRequest::class
    )
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Successful response',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                ref: OtpCodeResource::class,
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
class SendOtpCodeController extends ApiController
{
    public function __construct(protected OtpServiceInterface $otpService) {}

    /**
     * @throws ValidationException
     */
    public function __invoke(SendOtpRequest $request): JsonResponse
    {
        try {
            $otpDto = $this->otpService->send(
                recipient: $request->string('phone_number'),
                type: OtpTypeEnum::LOGIN,
                via: OtpViaEnum::ANY,
            );
        } catch (OtpAlreadySentException) {
            throw ValidationException::withMessages([
                'phone_number' => [
                    __('A valid OTP code has already been sent to this phone number.'),
                ],
            ]);
        }

        return ApiResponse::ok(
            OtpCodeResource::make($otpDto)
        );
    }
}
