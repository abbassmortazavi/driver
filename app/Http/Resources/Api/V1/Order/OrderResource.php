<?php

namespace App\Http\Resources\Api\V1\Order;

use App\Http\Resources\Api\V1\LoadingLocationResource;
use App\Http\Resources\Api\V1\Shipment\ShipmentResource;
use App\Http\Resources\Api\V1\Transport\TransportResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\OrderStatusEnum;

#[OA\Schema(
    required: [
        'id',
        'reference_number',
        'status',
        'description',
        'total_price_value',
        'total_price_currency',
        'origin_loading_location',
        'destination_loading_location',
        'expected_delivery_date',
        'allow_price_negotiation',
    ],
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'reference_number', type: 'string'),
        new OA\Property(property: 'status', type: 'string', enum: OrderStatusEnum::class),
        new OA\Property(property: 'description', type: 'string'),
        new OA\Property(property: 'total_price_value', type: 'decimal'),
        new OA\Property(property: 'total_price_currency', type: 'string'),
        new OA\Property(property: 'origin_loading_location', ref: LoadingLocationResource::class),
        new OA\Property(property: 'destination_loading_location', ref: LoadingLocationResource::class),
        new OA\Property(property: 'expected_delivery_date', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'allow_price_negotiation', type: 'boolean'),
        new OA\Property(property: 'shipments_count', type: 'string'),
        new OA\Property(
            property: 'transports',
            type: 'array',
            items: new OA\Items(ref: TransportResource::class)
        ),
        new OA\Property(
            property: 'shipments',
            type: 'array',
            items: new OA\Items(ref: ShipmentResource::class)
        ),
    ],
)]
class OrderResource extends JsonResource
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
            'reference_number' => $this->reference_number ?? null,
            'status' => $this->status ?? null,
            'description' => $this->description ?? null,
            'total_price_value' => $this->total_price_value ?? null,
            'total_price_currency' => $this->total_price_currency ?? null,
            'origin_loading_location' => LoadingLocationResource::make($this->originLoadingLocation),
            'destination_loading_location' => LoadingLocationResource::make($this->destinationLoadingLocation),
            'expected_delivery_date' => $this->expected_delivery_date ?? null,
            'allow_price_negotiation' => $this->allow_price_negotiation ?? null,
            'shipments_count' => $this->whenCounted('shipments'),
            'transports' => TransportResource::collection($this->whenLoaded('transports')),
            'shipments' => ShipmentResource::collection($this->whenLoaded('shipments')),
        ];
    }
}
