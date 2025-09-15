<?php

namespace App\Http\Resources\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: [
        'access_token',
        'expires_in',
        'refresh_token',
        'token_type',
    ],
    properties: [
        new OA\Property(property: 'access_token', type: 'string'),
        new OA\Property(property: 'expires_in', type: 'integer'),
        new OA\Property(property: 'refresh_token', type: 'string'),
        new OA\Property(property: 'token_type', type: 'string', enum: ['Bearer']),
    ],
)]
class AccessTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'access_token' => $this['access_token'],
            'expires_in' => $this['expires_in'],
            'refresh_token' => $this['refresh_token'],
            'token_type' => $this['token_type'],
        ];
    }
}
