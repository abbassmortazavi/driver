<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\StationStatusEnum;

#[OA\Schema(
    required: [
        'id',
        'mdm_organization_id',
        'status',
        'name',
        'description',
        'carrier_id',
        'short_name',
        'public_name',
        'start_opening_time',
        'end_opening_hour',
        'injection_days',
        'required_time_slot',
        'address_country',
        'address_city',
        'address_state',
        'address_street_and_house_number',
        'address_postal_code',
        'address_lat',
        'address_lng',
        'availability',
        'stop_type',
        'vehicle_restriction',
    ],
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'mdm_organization_id', type: 'string'),
        new OA\Property(property: 'status', type: 'string', enum: StationStatusEnum::class),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'description', type: 'string'),
        new OA\Property(property: 'carrier_id', type: 'string'),
        new OA\Property(property: 'short_name', type: 'string'),
        new OA\Property(property: 'public_name', type: 'string'),
        new OA\Property(property: 'start_opening_time', type: 'time'),
        new OA\Property(property: 'end_opening_hour', type: 'time'),
        new OA\Property(property: 'injection_days', type: 'string'),
        new OA\Property(property: 'required_time_slot', type: 'boolean'),
        new OA\Property(property: 'address_country', type: 'string'),
        new OA\Property(property: 'address_city', type: 'string'),
        new OA\Property(property: 'address_state', type: 'string'),
        new OA\Property(property: 'address_street_and_house_number', type: 'string'),
        new OA\Property(property: 'address_postal_code', type: 'string'),
        new OA\Property(property: 'address_lat', type: 'decimal'),
        new OA\Property(property: 'address_lng', type: 'decimal'),
        new OA\Property(property: 'availability', type: 'string'),
        new OA\Property(property: 'stop_type', type: 'string'),
        new OA\Property(property: 'vehicle_restriction', type: 'string'),
    ],
)]
class StationResource extends JsonResource
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
            'mdm_organization_id' => $this->mdm_organization_id,
            'status' => $this->status,
            'name' => $this->name,
            'description' => $this->description,
            'carrier_id' => $this->carrier->getHashedId(),
            'short_name' => $this->short_name,
            'public_name' => $this->public_name,
            'start_opening_time' => $this->start_opening_time,
            'end_opening_hour' => $this->end_opening_hour,
            'injection_days' => $this->injection_days,
            'required_time_slot' => $this->required_time_slot,
            'address_country' => $this->address_country,
            'address_city' => $this->address_city,
            'address_state' => $this->address_state,
            'address_street_and_house_number' => $this->address_street_and_house_number,
            'address_postal_code' => $this->address_postal_code,
            'address_lat' => $this->address_lat,
            'address_lng' => $this->address_lng,
            'availability' => $this->availability,
            'stop_type' => $this->stop_type,
            'vehicle_restriction' => $this->vehicle_restriction,
        ];
    }
}
