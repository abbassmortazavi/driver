<?php

namespace App\Http\Resources\Api\V1\Driver;

use App\Http\Resources\Api\V1\Vehicle\VehicleTypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\VehicleFuelTypeEnum;
use Patoughi\Common\Enums\VehicleStatusEnum;

#[OA\Schema(
    required: [
        'id',
        'name',
        'vehicle_type',
        'status',
        'fuel_type',
        'plate_number',
        'vin_number',
        'insurance_policy_number',
        'insurance_expiry_date',
        'color',
        'model_name',
        'brand_name',
        'year',
    ],
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'vehicle_type', type: 'object'),
        new OA\Property(property: 'status', type: 'string', enum: VehicleStatusEnum::class),
        new OA\Property(property: 'fuel_type', type: 'string', enum: VehicleFuelTypeEnum::class),
        new OA\Property(property: 'plate_number', type: 'string'),
        new OA\Property(property: 'vin_number', type: 'string'),
        new OA\Property(property: 'insurance_policy_number', type: 'string'),
        new OA\Property(property: 'insurance_expiry_date', type: 'string', format: 'date-time'),
        new OA\Property(property: 'color', type: 'string'),
        new OA\Property(property: 'model_name', type: 'string'),
        new OA\Property(property: 'brand_name', type: 'string'),
        new OA\Property(property: 'year', type: 'string'),
    ],
)]
class DriverVehicleResource extends JsonResource
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
            'vehicle_type' => VehicleTypeResource::make($this->vehicleType),
            'status' => $this->status,
            'fuel_type' => $this->fuel_type,
            'plate_number' => $this->plate_number,
            'vin_number' => $this->vin_number,
            'insurance_policy_number' => $this->insurance_policy_number,
            'insurance_expiry_date' => $this->insurance_expiry_date,
            'color' => $this->color,
            'model_name' => $this->model_name,
            'brand_name' => $this->brand_name,
            'year' => $this->year,
        ];
    }
}
