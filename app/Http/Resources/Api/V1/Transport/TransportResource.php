<?php

namespace App\Http\Resources\Api\V1\Transport;

use App\Http\Resources\Api\V1\Order\OrderResource;
use App\Http\Resources\Api\V1\Shipment\ShipmentResource;
use App\Http\Resources\Api\V1\Vehicle\VehicleResource;
use App\Http\Resources\Api\V1\Vehicle\VehicleTypeResource;
use App\Http\Resources\Api\V1\Waybill\WaybillResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\TransportAssignedDriverStatusEnum;
use Patoughi\Common\Enums\TransportStatusEnum;

#[OA\Schema(
    required: ['id', 'status', 'vehicle_type', 'vehicle', 'shipments', 'order', 'assignment_status', 'waybills'],
    properties: [
        new OA\Property(property: 'id', type: 'string', example: '01JTZQ30'),
        new OA\Property(property: 'status', type: 'string', enum: TransportStatusEnum::class),
        new OA\Property(property: 'vehicle_type', ref: VehicleTypeResource::class),
        new OA\Property(property: 'vehicle', ref: VehicleResource::class),
        new OA\Property(property: 'order', ref: OrderResource::class),
        new OA\Property(
            property: 'shipments',
            type: 'array',
            items: new OA\Items(ref: ShipmentResource::class)
        ),
        new OA\Property(
            property: 'waybills',
            type: 'array',
            items: new OA\Items(ref: WaybillResource::class)
        ),
        new OA\Property(property: 'assignment_status', type: 'string', enum: TransportAssignedDriverStatusEnum::class),
    ],
    type: 'object'
)]
class TransportResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getHashedId(),
            'status' => $this->status ?? null,
            'vehicle_type' => VehicleTypeResource::make($this->vehicleType) ?? null,
            'vehicle' => VehicleResource::make($this->whenLoaded('vehicle')),
            'shipments' => ShipmentResource::collection(collect($this->shipments ?? null)),
            'waybills' => WaybillResource::collection(collect($this->waybills ?? null)),
            'order' => OrderResource::make($this->whenLoaded('order')),
            'assignment_status' => $this->assignment_status ?? null,
        ];
    }
}
