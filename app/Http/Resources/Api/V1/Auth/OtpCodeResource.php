<?php

namespace App\Http\Resources\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: [
        'recipient',
        'expires_at',
        'length',
    ],
    properties: [
        new OA\Property(property: 'recipient', type: 'string'),
        new OA\Property(property: 'plain_code', type: 'string', nullable: true),
        new OA\Property(property: 'expires_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'length', type: 'integer'),
    ],
)]
class OtpCodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\OtpCode|self $this */
        return [
            'recipient' => $this->recipient,
            'plain_code' => $this->plainCode,
            'expires_at' => $this->expired_at,
            'length' => $this->length,
        ];
    }
}
