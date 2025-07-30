<?php

namespace App\Http\Resources\Api\V1\Bid;

use App\Http\Resources\Api\V1\Transport\TransportResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['id', 'status', 'proposed_price', 'description', 'transport'],
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'status', type: 'string', example: 'PENDING'),
        new OA\Property(property: 'proposed_price', type: 'number', format: 'float', example: 1000000),
        new OA\Property(property: 'description', type: 'string', example: 'I can deliver this shipment on time'),
        new OA\Property(property: 'transport', ref: TransportResource::class),
    ]
)]
class BidListResource extends JsonResource
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
            'proposed_price' => $this->proposed_price,
            'description' => $this->description,
            'transport' => TransportResource::make($this->whenLoaded('transport')),
        ];
    }
}
