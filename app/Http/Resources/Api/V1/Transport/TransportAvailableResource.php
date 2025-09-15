<?php

namespace App\Http\Resources\Api\V1\Transport;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['id', 'origin', 'destination', 'date', 'price', 'negotiation_allowed'],
    properties: [
        new OA\Property(property: 'id', type: 'string', example: '87Q1pakwnVo4'),
        new OA\Property(property: 'origin', type: 'string', example: 'babol'),
        new OA\Property(property: 'destination', type: 'string', example: 'rasht'),
        new OA\Property(property: 'date', type: 'string', example: '2025-03-05T16:53:53.000000Z'),
        new OA\Property(property: 'price', type: 'string', example: '51821126'),
        new OA\Property(property: 'negotiation_allowed', type: 'bool', example: true),
    ]
)]
class TransportAvailableResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getHashedId(),
            'origin' => $this?->order?->originLoadingLocation->name ?? null,
            'destination' => $this?->order?->destinationLoadingLocation->name ?? null,
            'date' => $this?->order?->expected_delivery_date,
            'price' => $this?->order?->total_price_value,
            'negotiation_allowed' => $this?->order?->allow_price_negotiation,
            'allowed_to_bid' => !$this->bids()->exists() ?? true,
        ];
    }
}
