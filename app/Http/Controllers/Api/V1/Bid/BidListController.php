<?php

namespace App\Http\Controllers\Api\V1\Bid;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Bid\ListBidRequest;
use App\Http\Resources\Api\V1\Bid\BidListResource;
use App\Http\Responses\Api\V1\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\BidStatusEnum;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

#[OA\Get(
    path: '/api/v1/bids',
    operationId: 'ListMyBids',
    summary: 'List driver\'s submitted bids',
    security: [['bearerAuth' => []]],
    tags: ['Bid'],
    parameters: [
        new OA\Parameter(
            name: 'page',
            description: 'Page number',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'integer', default: 1)
        ),
        new OA\Parameter(
            name: 'per_page',
            description: 'Number of items per page (default: 50)',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'integer', default: 50, minimum: 1)
        ),
        new OA\Parameter(
            name: 'status',
            description: 'Status',
            in: 'query',
            required: false,
            schema: new OA\Schema(
                type: 'string',
                enum: BidStatusEnum::class,
            )
        ),

    ]
)]
#[OA\Response(
    response: ResponseAlias::HTTP_OK,
    description: 'Successful response',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'success',
                type: 'boolean',
                example: true
            ),
            new OA\Property(
                property: 'data',
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/BidListResource')
            ),
            new OA\Property(
                property: 'meta',
                properties: [
                    new OA\Property(property: 'current_page', type: 'integer', example: 1),
                    new OA\Property(property: 'per_page', type: 'integer', example: 50), // Updated to match default
                    new OA\Property(property: 'total', type: 'integer', example: 100),
                ],
                type: 'object'
            ),
        ],
        type: 'object'
    )
)]
#[OA\Response(
    response: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
    description: 'Validation error',
    content: new OA\JsonContent(
        ref: '#/components/schemas/ApiResponseErrorValidation',
    )
)]
#[OA\Response(
    response: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
    description: 'Server error during registration',
    content: new OA\JsonContent(
        ref: '#/components/schemas/ApiResponseErrorServer',
    )
)]
class BidListController extends ApiController
{
    /**
     * Handle the incoming request.
     *
     * @param  ListBidRequest  $request
     * @return JsonResponse
     */
    public function __invoke(ListBidRequest $request): JsonResponse
    {
        $driver = auth()->user()->driver;
        $bids = $driver->bids()->with(['transport', 'transport.order'])
            ->when($request->status, fn ($q) => $q->where('status', $request->get('status')))
            ->latest()
            ->paginate($request->integer('per_page', 50));

        return ApiResponse::ok(BidListResource::collection($bids), [
            'current_page' => $bids->currentPage(),
            'per_page' => $bids->perPage(),
            'total' => $bids->total(),
        ]);
    }
}
