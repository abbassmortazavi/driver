<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\LoadingLocationStatusEnum;

#[OA\Schema(
    required: [
        'id',
        'mdm_organization_id',
        'status',
        'name',
        'description',
        'station_id',
        'usage',
        'driver_instruction',
    ],
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'mdm_organization_id', type: 'string'),
        new OA\Property(property: 'status', type: 'string', enum: LoadingLocationStatusEnum::class),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'description', type: 'string'),
        new OA\Property(property: 'station_id', type: 'string'),
        new OA\Property(property: 'usage', type: 'string'),
        new OA\Property(property: 'driver_instruction', type: 'string'),
    ],
)]
class LoadingLocationResource extends JsonResource
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
            'station_id' => $this->station->getHashedId(),
            'usage' => $this->usage,
            'driver_instruction' => $this->driver_instruction,
        ];
    }
}
