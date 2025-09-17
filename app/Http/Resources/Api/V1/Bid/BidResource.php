<?php

namespace App\Http\Resources\Api\V1\Bid;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\BidStatusEnum;

#[OA\Schema(
    required: ['id', 'driver_id', 'transport_id', 'proposed_price', 'description', 'status'],
    properties: [
        new OA\Property(property: 'driver_id', type: 'string'),
        new OA\Property(property: 'transport_id', type: 'string'),
        new OA\Property(property: 'proposed_price', type: 'string'),
        new OA\Property(property: 'description', type: 'string'),
        new OA\Property(property: 'status', type: 'string', enum: BidStatusEnum::class),
    ]
)]
class BidResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getHashedId(),
            'driver_id' => $this?->driver->full_name,
            'transport_id' => $this->transport->getHashedId(),
            'proposed_price' => $this->proposed_price,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }
}
