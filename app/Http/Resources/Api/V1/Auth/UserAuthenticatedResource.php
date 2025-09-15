<?php

namespace App\Http\Resources\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: [
        'is_authenticated',
    ],
    properties: [
        new OA\Property(property: 'is_authenticated', type: 'boolean'),
    ],
)]
class UserAuthenticatedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'is_authenticated' => $this['is_authenticated'] ?? false,
        ];
    }
}
