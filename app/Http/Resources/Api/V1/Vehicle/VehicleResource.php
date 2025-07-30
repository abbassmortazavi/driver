<?php

namespace App\Http\Resources\Api\V1\Vehicle;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\VehicleFuelTypeEnum;
use Patoughi\Common\Enums\VehicleStatusEnum;

#[OA\Schema(
    schema: 'VehicleResource',
    title: 'Vehicle Resource',
    description: 'Vehicle resource response',
    required: [
        'id',
        'name',
        'vehicle_type_id',
        'status',
        'fuel_type',
        'plate_number',
        'vin_number',
        'insurance_policy_number',
        'insurance_expiry_date',
        'current_location_lat',
        'current_location_lng',
        'color',
        'model_name',
        'brand_name',
        'year',
        'created_at',
        'updated_at',
        'is_allow_delete',
    ],
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'vehicle_type_id', type: 'string'),
        new OA\Property(property: 'status', type: 'string', enum: VehicleStatusEnum::class),
        new OA\Property(property: 'fuel_type', type: 'string', enum: VehicleFuelTypeEnum::class),
        new OA\Property(property: 'plate_number', type: 'string'),
        new OA\Property(property: 'vin_number', type: 'string'),
        new OA\Property(property: 'insurance_policy_number', type: 'string'),
        new OA\Property(property: 'insurance_expiry_date', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'current_location_lat', type: 'decimal'),
        new OA\Property(property: 'current_location_lng', type: 'decimal'),
        new OA\Property(property: 'color', type: 'string'),
        new OA\Property(property: 'model_name', type: 'string'),
        new OA\Property(property: 'brand_name', type: 'string'),
        new OA\Property(property: 'year', type: 'string'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'is_allow_delete', type: 'boolean', example: true),
    ],
)]
class VehicleResource extends JsonResource
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
            'vehicle_type' => VehicleTypeResource::make($this->vehicleType) ?? null,
            'plate_number' => $this->plate_number,
            'color' => $this->color,
            'status' => $this->status,
            'fuel_type' => $this->fuel_type,
            'vin_number' => $this->vin_number,
            'insurance_policy_number' => $this->insurance_policy_number,
            'insurance_expiry_date' => $this->insurance_expiry_date,
            'current_location_lat' => $this->current_location_lat,
            'current_location_lng' => $this->current_location_lng,
            'model_name' => $this->model_name,
            'brand_name' => $this->brand_name,
            'year' => $this->year,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_allow_delete' => $this->checkVehicleAllowToDelete($this),
        ];
    }

    /**
     * @param  $vehicle
     * @return bool
     */
    private function checkVehicleAllowToDelete($vehicle): bool
    {
        return $vehicle->status === VehicleStatusEnum::INACTIVE->value && ! $vehicle->transports()->exists();
    }
}
