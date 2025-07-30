<?php

namespace App\Http\Resources\Api\V1\Transport;

use App\Http\Resources\Api\V1\Bid\BidResource;
use App\Http\Resources\Api\V1\Order\OrderResource;
use App\Http\Resources\Api\V1\Shipment\ShipmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['id', 'status', 'order', 'shipments', 'bids'],
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'status', type: 'string', example: 'AWAITING_BID'),
        new OA\Property(property: 'order', ref: OrderResource::class),
        new OA\Property(
            property: 'shipments',
            type: 'array',
            items: new OA\Items(ref: ShipmentResource::class)
        ),
        new OA\Property(
            property: 'bids',
            type: 'array',
            items: new OA\Items(ref: BidResource::class)
        ),
    ]
)]
class TransportDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getHashedId(),
            'status' => $this->status,
            'order' => OrderResource::make($this->whenLoaded('order')),
            'shipments' => ShipmentResource::collection($this->whenLoaded('shipments')),
            'bids' => BidResource::collection($this->whenLoaded('driverBids')),
        ];
    }
}
