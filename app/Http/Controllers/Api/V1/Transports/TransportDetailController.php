<?php

namespace App\Http\Controllers\Api\V1\Transports;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\Transport\TransportDetailResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Models\Transport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Get(
    path: '/api/v1/transports/{transport}',
    operationId: 'getTransportDetail',
    summary: 'Get transport details',
    security: [['bearerAuth' => []]],
    tags: ['Transport'],
    parameters: [
        new OA\Parameter(
            name: 'transport',
            description: 'Transport ID',
            in: 'path',
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
                property: 'success',
                type: 'boolean',
                example: true
            ),
            new OA\Property(
                property: 'data',
                ref: TransportDetailResource::class,
                type: 'object'
            ),
        ],
        type: 'object'
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
class TransportDetailController extends ApiController
{
    /**
     * @param  Transport  $transport
     * @return JsonResponse
     */
    public function __invoke(Transport $transport): JsonResponse
    {
        $driver = auth()->user()->driver;

        Gate::authorize('view', $driver);

        if (auth()->check()) {
            $transport->setRelation('driverBids',
                $transport->bids()->where('driver_id', auth()->user()->driver->getKey())->get()
            );
        }
        $transport->load(['order', 'shipments']);

        return ApiResponse::ok(TransportDetailResource::make($transport));
    }
}
