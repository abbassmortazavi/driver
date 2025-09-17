<?php

namespace App\Http\Resources\Api\V1\TransportTrackingLog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['lat', 'lng', 'driver', 'recorded_at', 'transport'],
    properties: [
        new OA\Property(property: 'lat', type: 'string', example: '45.1254662'),
        new OA\Property(property: 'lng', type: 'string', example: '-122.456789'),
        new OA\Property(property: 'driver', type: 'string', example: 'Abbass'),
        new OA\Property(property: 'transport', type: 'string', example: 'peykan'),
        new OA\Property(property: 'recorded_at', type: 'string', example: '2025-05-20 12:17:50'),
    ]
)]
class TransportTrackingLogResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
            'driver' => $this?->driver->full_name,
            'transport' => $this->transport?->vehicleType->name,
            'recorded_at' => $this->recorded_at,
        ];
    }
}
