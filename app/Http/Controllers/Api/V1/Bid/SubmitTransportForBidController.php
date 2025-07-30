<?php

namespace App\Http\Controllers\Api\V1\Bid;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Bid\CreateBidRequest;
use App\Http\Resources\Api\V1\Bid\BidResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Models\Bid;
use App\Models\Transport;
use App\Repository\Bid\BidRepositoryInterface;
use App\Repository\Transport\TransportRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Post(
    path: '/api/v1/transports/{transport}/bids',
    operationId: 'storeBid',
    summary: 'Store Bid',
    security: [['bearerAuth' => []]],
    tags: ['Bid'],
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
#[OA\RequestBody(
    description: 'Store Bid',
    required: true,
    content: new OA\JsonContent(
        ref: CreateBidRequest::class
    )
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Successful response',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                ref: BidResource::class,
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
class SubmitTransportForBidController extends ApiController
{
    /**
     * @param  BidRepositoryInterface  $bidRepository
     * @param  TransportRepositoryInterface  $transportRepository
     */
    public function __construct(
        protected BidRepositoryInterface $bidRepository,
        protected TransportRepositoryInterface $transportRepository
    ) {}

    /**
     * @param  CreateBidRequest  $request
     * @param  Transport  $transport
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function __invoke(CreateBidRequest $request, Transport $transport): JsonResponse
    {
        Gate::authorize('hasBidForCurrentDriver', $transport);
        Gate::authorize('checkExistsShipment', [Bid::class, $transport]);
        Gate::authorize('checkExistsOrder', [Bid::class, $transport]);
        Gate::authorize('allowPriceForNegotiation', [Bid::class, $transport]);
        $data = $this->prepareData($transport, $request);

        return ApiResponse::ok(BidResource::make($this->bidRepository->create($data)));
    }

    /**
     * @param  Transport  $transport
     * @param  $request
     * @return array
     */
    private function prepareData(Transport $transport, $request)
    {
        return [
            'driver_id' => auth()->user()->driver->getKey(),
            'transport_id' => $transport->id,
            'proposed_price' => $request->float('proposed_price'),
            'description' => $request->description,
        ];
    }
}
