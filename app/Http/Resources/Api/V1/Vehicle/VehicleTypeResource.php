<?php

namespace App\Http\Resources\Api\V1\Vehicle;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\VehicleTripTypeEnum;

#[OA\Schema(
    required: [
        'id',
        'name',
        'name_en',
        'vehicle_length',
        'vehicle_width',
        'capacity_pal',
        'capacity_weight',
        'max_loading_height',
        'trip_type',
        'rtmp_code',
        'rtmp_min_capacity',
        'rtmp_max_capacity',
        'created_at',
        'updated_at',
    ],
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'name_en', type: 'string'),
        new OA\Property(property: 'vehicle_length', type: 'double'),
        new OA\Property(property: 'vehicle_width', type: 'double'),
        new OA\Property(property: 'capacity_pal', type: 'integer'),
        new OA\Property(property: 'capacity_weight', type: 'integer'),
        new OA\Property(property: 'max_loading_height', type: 'integer'),
        new OA\Property(property: 'trip_type', type: 'string', enum: VehicleTripTypeEnum::class),
        new OA\Property(property: 'rtmp_code', type: 'integer'),
        new OA\Property(property: 'rtmp_min_capacity', type: 'integer'),
        new OA\Property(property: 'rtmp_max_capacity', type: 'integer'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ],
)]
class VehicleTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getHashedId(),
            'name' => $this->name,
            'name_en' => $this->name_en,
            'vehicle_length' => $this->vehicle_length,
            'vehicle_width' => $this->vehicle_width,
            'capacity_pal' => $this->capacity_pal,
            'capacity_weight' => $this->capacity_weight,
            'max_loading_height' => $this->max_loading_height,
            'trip_type' => $this->trip_type,
            'rtmp_code' => $this->rtmp_code,
            'rtmp_min_capacity' => $this->rtmp_min_capacity,
            'rtmp_max_capacity' => $this->rtmp_max_capacity,
        ];
    }
}
