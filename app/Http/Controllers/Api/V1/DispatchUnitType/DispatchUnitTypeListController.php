<?php

namespace App\Http\Controllers\Api\V1\DispatchUnitType;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\DispatchUnitTypeResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Repository\DispatchUnitType\DispatchUnitTypeRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/v1/dispatch-unit-type',
    operationId: 'dispatch-unit-type-list',
    summary: 'Retrieve list of Dispatch Unit Types',
    tags: ['General'],
)]
#[OA\Response(
    response: 200,
    description: 'Successful response',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                ref: DispatchUnitTypeResource::class,
                type: 'object',
            ),
        ],
    )
)]
class DispatchUnitTypeListController extends ApiController
{
    public function __construct(protected DispatchUnitTypeRepositoryInterface $dispatchUnitTypeRepository) {}

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        return ApiResponse::ok(
            DispatchUnitTypeResource::collection($this->dispatchUnitTypeRepository->all()),
        );
    }
}
