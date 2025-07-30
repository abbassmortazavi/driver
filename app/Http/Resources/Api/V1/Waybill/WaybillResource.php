<?php

namespace App\Http\Resources\Api\V1\Waybill;

use App\Http\Resources\Api\V1\Driver\DriverResource;
use App\Http\Resources\Api\V1\Transport\TransportResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['id', 'driver', 'transport', 'proposed_price'],
    properties: [
        new OA\Property(property: 'id', type: 'string', example: '01JTZQ30ZR13VDZGMQCHDCB5MB'),
        new OA\Property(
            property: 'driver',
            type: 'array',
            items: new OA\Items(type: 'string', example: ['id' => 1, 'full_name' => 'Abbass Alavy']),
        ),
        new OA\Property(
            property: 'transport',
            type: 'array',
            items: new OA\Items(type: 'string', example: ['id' => 1, 'full_name' => 'Abbass Alavy']),
        ),
        new OA\Property(property: 'proposed_price', type: 'string', example: 2),
    ],
    type: 'object'
)]
class WaybillResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getHashedId(),
            'proposed_price' => $this->price_value ?? null,
            'driver' => DriverResource::make($this->whenLoaded('driver')),
            'transport' => TransportResource::make($this->whenLoaded('transport')),
        ];
    }
}
