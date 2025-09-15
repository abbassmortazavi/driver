<?php

namespace App\Http\Controllers\Api\V1\Bid;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Models\Bid;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Patoughi\Common\Orm\StatesMachine\Bid\State\BidCancelled;
use Symfony\Component\HttpFoundation\Response;

#[OA\Delete(
    path: '/api/v1/bids/{bid}',
    operationId: 'cancelBid',
    summary: 'Cancel a bid',
    security: [['bearerAuth' => []]],
    tags: ['Bid'],
    parameters: [
        new OA\Parameter(
            name: 'bid',
            description: 'Bid ID',
            in: 'path',
            required: true,
            schema: new OA\Schema(
                type: 'string',
            )
        ),
    ]
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Bid cancelled successfully',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                properties: [
                    new OA\Property(property: 'status', type: 'string', example: 'cancelled'),
                ],
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
class CancelBidController extends ApiController
{
    /**
     * @param  Bid  $bid
     * @return JsonResponse
     */
    public function __invoke(Bid $bid): JsonResponse
    {
        Gate::authorize('cancel', $bid);

        $bid->status->transitionTo(BidCancelled::class);

        return ApiResponse::ok([
            'status' => 'cancelled',
        ]);
    }
}
