<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Dto\Auth\AccessTokenDto;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Auth\AccessTokenRequest;
use App\Http\Resources\Api\V1\Auth\AccessTokenResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Models\PublicUser;
use App\Repository\User\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Http\Controllers\AccessTokenController as OauthAccessTokenController;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\OtpTypeEnum;
use Patoughi\Common\Services\Otp\OtpServiceInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Post(
    path: '/api/v1/auth/token',
    operationId: 'accessToken',
    summary: 'Generate a New Access Token',
    tags: ['Auth'],
)]
#[OA\RequestBody(
    required: true,
    content: new OA\JsonContent(ref: AccessTokenRequest::class)
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Successful response',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                ref: AccessTokenResource::class,
                type: 'object'
            ),
        ])
)]
#[OA\Response(
    response: Response::HTTP_UNPROCESSABLE_ENTITY,
    description: 'Validation error',
    content: new OA\JsonContent(
        ref: '#/components/schemas/ApiResponseErrorValidation'
    )
)]
#[OA\Response(
    response: Response::HTTP_INTERNAL_SERVER_ERROR,
    description: 'Server error during registration',
    content: new OA\JsonContent(
        ref: '#/components/schemas/ApiResponseErrorServer'
    )
)]
class AccessTokenController extends ApiController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        protected OtpServiceInterface $otpService
    ) {}

    /**
     * @param  AccessTokenRequest  $request
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function __invoke(AccessTokenRequest $request): JsonResponse
    {
        if (
            ! $this->otpService->verify(
                recipient: $request->input('phone_number'),
                type: OtpTypeEnum::LOGIN,
                code: $request->input('code')
            )
        ) {
            throw ValidationException::withMessages([
                'code' => [
                    __('The OTP code is invalid or has expired.'),
                ],
            ]);
        }

        $data = DB::transaction(function () use ($request) {
            $user = $this->userRepository
                ->findByPhoneNumber(
                    $request->input('phone_number')
                );

            if (! $user instanceof PublicUser) {
                $user = $this->registerNewUser($request);
            }

            $accessTokenDto = $this->getAccessToken($user);

            $this->userRepository->update($user->id, ['last_login_at' => now()]);

            return [
                'access_token' => $accessTokenDto->getAccessToken(),
                'refresh_token' => $accessTokenDto->getRefreshToken(),
                'expires_in' => $accessTokenDto->getExpiresIn(),
                'token_type' => $accessTokenDto->getTokenType(),
            ];
        });

        return ApiResponse::ok(
            data: AccessTokenResource::make($data),
        );

    }

    /**
     * @param AccessTokenRequest $request
     * @return PublicUser
     */
    private function registerNewUser(AccessTokenRequest $request): PublicUser
    {
        return $this->userRepository->create($this->prepareRegisterData($request));
    }

    /**
     * @param AccessTokenRequest $request
     * @return array
     */
    public function prepareRegisterData(AccessTokenRequest $request): array
    {
        return [
            'phone_number' => $request->input('phone_number') ?? null,
            'phone_number_verify_at' => Carbon::now(),
            'password' => bcrypt('patoghi'),
        ];
    }

    /**
     * @throws Throwable
     */
    protected function getAccessToken(PublicUser $user): AccessTokenDto
    {
        $request = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => config('passport.password_grant_client.id'),
            'client_secret' => config('passport.password_grant_client.secret'),
            'username' => $user->phone_number,
            'password' => '',
            'scope' => '',
        ]);

        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrRequest = $psrHttpFactory->createRequest($request);

        $response = app()
            ->make(OauthAccessTokenController::class)
            ->issueToken($psrRequest);

        $data = json_decode($response->getContent(), true);

        return new AccessTokenDto(
            accessToken: data_get($data, 'access_token'),
            refreshToken: data_get($data, 'refresh_token'),
            expiresIn: data_get($data, 'expires_in'),
            tokenType: data_get($data, 'token_type'),
        );
    }
}
