<?php

namespace App\Http\Controllers\Api\V1\Transports;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\Api\V1\Transport\TransportAvailableResource;
use App\Http\Responses\Api\V1\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\TransportStatusEnum;
use Symfony\Component\HttpFoundation\Response;

#[OA\Get(
    path: '/api/v1/transports/available',
    operationId: 'GetAvailableTransports',
    summary: 'Get available transports',
    security: [['bearerAuth' => []]],
    tags: ['Transport'],
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
class AvailableTransportsController extends ApiController
{
    /**
     * @return JsonResponse
     */
    public function __invoke()
    {
        $driver = auth()->user()->driver;
        Gate::authorize('view', $driver);
        $transports = $driver->transports()
            ->with('order')
            ->where('status', '=', TransportStatusEnum::PENDING->value)
            ->get()
            ->sortByDesc(function ($transport) {
                return optional($transport->order)->expected_delivery_date;
            });

        return ApiResponse::ok(TransportAvailableResource::collection($transports));
    }
}
