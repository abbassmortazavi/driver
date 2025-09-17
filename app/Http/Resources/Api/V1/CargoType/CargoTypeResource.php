<?php

namespace App\Http\Resources\Api\V1\CargoType;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: [
        'id',
        'name',
        'description',
    ],
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'description', type: 'string'),
    ],
)]

class CargoTypeResource extends JsonResource
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
            'description' => $this->description,
        ];
    }
}
