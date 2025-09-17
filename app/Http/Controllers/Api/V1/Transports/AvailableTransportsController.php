<?php

namespace App\Http\Controllers\Api\V1\Transports;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\Transport\TransportAvailableResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Models\Transport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\BidStatusEnum;
use Patoughi\Common\Enums\TransportAssignmentStatusEnum;
use Patoughi\Common\Enums\TransportStatusEnum;
use Symfony\Component\HttpFoundation\Response;

#[OA\Get(
    path: '/api/v1/transports/available',
    operationId: 'getAvailableTransports',
    description: 'Returns a paginated list of transports available for bidding that the current driver hasn\'t already bid on.',
    summary: 'Get available transports',
    security: [['bearerAuth' => []]],
    tags: ['Transport'],
    parameters: [
        new OA\Parameter(
            name: 'per_page',
            description: 'Number of items per page',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'integer', default: 10)
        ),
        new OA\Parameter(
            name: 'page',
            description: 'Page number',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'integer', default: 1)
        ),
    ],
)]
#[OA\Response(
    response: Response::HTTP_OK,
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
                items: new OA\Items(ref: TransportAvailableResource::class)
            ),
            new OA\Property(
                property: 'meta',
                properties: [
                    new OA\Property(property: 'current_page', type: 'integer', example: 1),
                    new OA\Property(property: 'per_page', type: 'integer', example: 10),
                    new OA\Property(property: 'total', type: 'integer', example: 100),
                ],
                type: 'object'
            ),
        ],
        type: 'object'
    )
)]
#[OA\Response(
    response: Response::HTTP_UNAUTHORIZED,
    description: 'Unauthorized',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'success', type: 'boolean', example: false),
            new OA\Property(property: 'message', type: 'string', example: 'Unauthorized'),
            new OA\Property(property: 'code', type: 'integer', example: 401),
        ]
    )
)]
#[OA\Response(
    response: Response::HTTP_FORBIDDEN,
    description: 'Forbidden - Driver not authorized to view transports',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'success', type: 'boolean', example: false),
            new OA\Property(property: 'message', type: 'string', example: 'Forbidden'),
            new OA\Property(property: 'code', type: 'integer', example: 403),
        ]
    )
)]
class AvailableTransportsController extends ApiController
{
    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $driver = auth()->user()->driver;
        Gate::authorize('view', $driver);
        $transports = Transport::query()
            ->with('order')
            ->where('status', '=', TransportStatusEnum::PENDING->value)
            ->whereNull('driver_id')
            ->where('assignment_status', '=', TransportAssignmentStatusEnum::UNASSIGNED->value)
            ->whereDoesntHave('bids', function ($query) use ($driver) {
                $query->where('driver_id', $driver->id)
                    ->whereIn('status', [BidStatusEnum::PENDING, BidStatusEnum::ACCEPTED]);
            })
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 10));

        return ApiResponse::ok(TransportAvailableResource::collection($transports), [
            'current_page' => $transports->currentPage(),
            'per_page' => $transports->perPage(),
            'total' => $transports->total(),
        ]);
    }
}
