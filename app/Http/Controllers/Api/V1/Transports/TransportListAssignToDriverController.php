<?php

namespace App\Http\Controllers\Api\V1\Transports;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Transport\TransportToAssignedDriverListRequest;
use App\Http\Resources\Api\V1\Transport\TransportResource;
use App\Http\Responses\Api\V1\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Get(
    path: '/api/v1/transports',
    operationId: 'list-transports',
    description: 'Retrieves a paginated list of transports filtered by status (e.g., "assigned").',
    summary: 'List all transports assigned to driver',
    security: [['bearerAuth' => []]],
    tags: ['Transport'],
    responses: [
        new OA\Response(
            response: Response::HTTP_OK,
            description: 'Successfully retrieved transports',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'data',
                        type: 'array',
                        items: new OA\Items(ref: TransportResource::class)
                    ),
                    new OA\Property(
                        property: 'meta',
                        properties: [
                            new OA\Property(property: 'current_page', type: 'integer', example: 1),
                            new OA\Property(property: 'per_page', type: 'integer', example: 10),
                            new OA\Property(property: 'total', type: 'integer', example: 3),
                        ],
                        type: 'object'
                    ),
                ]
            )
        ),
        new OA\Response(
            response: Response::HTTP_UNAUTHORIZED,
            description: 'Authentication failed (invalid/missing token)',
            content: new OA\JsonContent(ref: '#/components/schemas/ApiResponseErrorUnauthorized')
        ),
        new OA\Response(
            response: Response::HTTP_UNPROCESSABLE_ENTITY,
            description: 'Validation error (e.g., invalid status or pagination parameters)',
            content: new OA\JsonContent(ref: '#/components/schemas/ApiResponseErrorValidation')
        ),
        new OA\Response(
            response: Response::HTTP_INTERNAL_SERVER_ERROR,
            description: 'Server error',
            content: new OA\JsonContent(ref: '#/components/schemas/ApiResponseErrorServer')
        ),
    ]
)]
class TransportListAssignToDriverController extends ApiController
{
    /**
     * @param  TransportToAssignedDriverListRequest  $request
     * @return JsonResponse
     */
    public function __invoke(TransportToAssignedDriverListRequest $request)
    {
        $driver = auth()->user()->driver;
        Gate::authorize('owner', $driver);
        $transports = $driver->transports()
            ->with(['vehicle', 'waybills'])
            ->when($request->status, function ($query) use ($request) {
                return $query->where('status', $request->status);
            })->paginate($request->integer('per_page'));

        return ApiResponse::ok(TransportResource::collection($transports->items()), [
            'current_page' => $transports->currentPage(),
            'per_page' => $transports->perPage(),
            'total' => $transports->total(),
        ]);
    }
}
