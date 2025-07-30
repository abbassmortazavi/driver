<?php

namespace App\Http\Controllers\Api\V1\Version;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Version\VersionRequest;
use App\Http\Resources\Api\V1\Version\VersionResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Repository\Version\VersionRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Get(
    path: '/api/v1/versions/check',
    operationId: 'Version',
    summary: 'Version',
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
            name: 'version',
            description: 'version',
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
class VersionController extends ApiController
{
    /**
     * @param  VersionRepositoryInterface  $repository
     */
    public function __construct(protected VersionRepositoryInterface $repository) {}

    /**
     * @param  VersionRequest  $request
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function __invoke(VersionRequest $request): JsonResponse
    {
        $currentVersion = $request->version;
        $latestVersion = $this->repository->getLatestVersion($request->platform, $request->type);
        if ($latestVersion) {
            if ($currentVersion > $latestVersion->version) {
                throw ValidationException::withMessages([
                    trans('messages.your_version_is_not_Valid'),
                ]);
            }
            $latest = $latestVersion->version;
            $latestVersion->force_update = $this->isUpdateRequired($currentVersion, $latest);

            return ApiResponse::ok(VersionResource::make($latestVersion));
        } else {
            throw ValidationException::withMessages([
                trans('messages.version_is_not_found'),
            ]);
        }
    }

    /**
     * @param  $current
     * @param  $latest
     * @return bool|int
     */
    private function isUpdateRequired($current, $latest): bool|int
    {
        return version_compare($current, $latest, '<');
    }
}
