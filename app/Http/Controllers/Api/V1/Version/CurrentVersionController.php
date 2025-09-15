<?php

namespace App\Http\Controllers\Api\V1\Version;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Version\VersionCheckRequest;
use App\Http\Requests\Version\VersionRequest;
use App\Http\Resources\Api\V1\Version\VersionResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Repository\Version\VersionRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Get(
    path: '/api/v1/versions',
    operationId: 'Get Latest Version',
    summary: 'Get Latest Version',
    tags: ['Version'],
    parameters: [
        new OA\Parameter(
            name: 'platform',
            description: 'Platform',
            in: 'query',
            required: true,
            schema: new OA\Schema(type: 'string')
        ),
        new OA\Parameter(
            name: 'type',
            description: 'Type',
            in: 'query',
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
                ref: VersionResource::class,
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
class CurrentVersionController extends ApiController
{
    /**
     * @param VersionRepositoryInterface $repository
     */
    public function __construct(protected VersionRepositoryInterface $repository)
    {
    }

    /**
     * @param VersionCheckRequest $request
     * @return JsonResponse
     *
     * @throws InvalidArgumentException
     */
    public function __invoke(VersionCheckRequest $request)
    {
        $cachedResult = Cache::get('lastestVersion');
        if ($cachedResult) {
            $data = json_decode($cachedResult);

            return ApiResponse::ok(VersionResource::make($data));
        }
        $latestVersion = $this->repository->getLatestVersion($request->platform, $request->type);
        if (is_null($latestVersion)) {
            throw ValidationException::withMessages([
                trans('messages.version_is_not_found'),
            ]);
        }

        Cache::set('lastestVersion', $latestVersion);

        return ApiResponse::ok(VersionResource::make($latestVersion));
    }
}
