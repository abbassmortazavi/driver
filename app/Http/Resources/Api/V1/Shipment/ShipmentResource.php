<?php

namespace App\Http\Resources\Api\V1\Shipment;

use App\Http\Resources\Api\V1\CargoType\CargoTypeResource;
use App\Http\Resources\Api\V1\DispatchUnitTypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\OrderStatusEnum;

#[OA\Schema(
    required: [
        'id',
        'tracking_number',
        'order_id',
        'status',
        'name',
        'dispatch_unit_type_id',
        'expected_pickup_at',
        'expected_drop_off_at',
        'total_weight',
        'total_volume',
        'description',
    ],
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'tracking_number', type: 'string'),
        new OA\Property(property: 'order_id', type: 'string'),
        new OA\Property(property: 'status', type: 'string', enum: OrderStatusEnum::class),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'dispatch_unit_type', ref: DispatchUnitTypeResource::class, nullable: true),
        new OA\Property(property: 'cargo_type', ref: CargoTypeResource::class, nullable: true),
        new OA\Property(property: 'expected_pickup_at',  format: 'date-time'),
        new OA\Property(property: 'expected_drop_off_at', type: 'date-time'),
        new OA\Property(property: 'total_weight', type: 'double'),
        new OA\Property(property: 'total_volume', type: 'double'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
    ],
)]
class ShipmentResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getHashedId(),
            'tracking_number' => $this->tracking_number ?? null,
            'order_id' => $this->order->getHashedId() ?? null,
            'status' => $this->status ?? null,
            'name' => $this->name ?? null,
            'dispatch_unit_type' => DispatchUnitTypeResource::make($this->dispatchUnitType),
            'cargo_type' => CargoTypeResource::make($this->cargoType),
            'expected_pickup_at' => $this->expected_pickup_at ?? null,
            'expected_drop_off_at' => $this->expected_drop_off_at ?? null,
            'total_weight' => $this->total_weight ?? null,
            'total_volume' => $this->total_volume ?? null,
            'description' => $this->description ?? null,
        ];
    }
}
