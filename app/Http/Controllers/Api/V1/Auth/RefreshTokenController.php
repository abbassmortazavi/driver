<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Dto\Auth\AccessTokenDto;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Auth\UserRefreshTokenRequest;
use App\Http\Resources\Api\V1\Auth\AccessTokenResource;
use App\Http\Responses\Api\V1\ApiResponse;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passport\Http\Controllers\AccessTokenController as OauthAccessTokenController;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenApi\Attributes as OA;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Response;

#[OA\Post(
    path: '/api/v1/auth/refresh-token',
    operationId: 'GetRefreshToken',
    summary: 'Get Refresh Token',
    security: [['bearerAuth' => []]],
    tags: ['Auth'],
)]
#[OA\RequestBody(
    description: 'Get Refresh Token',
    required: true,
    content: new OA\JsonContent(
        ref: UserRefreshTokenRequest::class
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
class RefreshTokenController extends ApiController
{
    /**
     * @throws BindingResolutionException
     */
    public function __invoke(UserRefreshTokenRequest $request): JsonResponse
    {
        $response = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => config('passport.password_grant_client.id'),
            'client_secret' => config('passport.password_grant_client.secret'),
            'scope' => '',
        ]);

        if ($response->json('error')) {
            $err['error'] = $response->json('error');

            return ApiResponse::error($response->json('error_description'), $err, Response::HTTP_UNAUTHORIZED);
        }

        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrRequest = $psrHttpFactory->createRequest($response);

        $response = app()
            ->make(OauthAccessTokenController::class)
            ->issueToken($psrRequest);

        $data = json_decode($response->getContent(), true);
        $accessTokenDto = $this->getAccessToken($data);

        return ApiResponse::ok(AccessTokenResource::make([
            'access_token' => $accessTokenDto->getAccessToken(),
            'refresh_token' => $accessTokenDto->getRefreshToken(),
            'expires_in' => $accessTokenDto->getExpiresIn(),
            'token_type' => $accessTokenDto->getTokenType(),
        ]));
    }

    /**
     * @param  array  $data
     * @return AccessTokenDto
     */
    protected function getAccessToken(array $data): AccessTokenDto
    {
        return new AccessTokenDto(
            accessToken: data_get($data, 'access_token'),
            refreshToken: data_get($data, 'refresh_token'),
            expiresIn: data_get($data, 'expires_in'),
            tokenType: data_get($data, 'token_type'),
        );
    }
}
