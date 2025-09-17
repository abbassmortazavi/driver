<?php

namespace App\Http\Resources\Api\V1\Version;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\VersionPlatformEnum;
use Patoughi\Common\Enums\VersionStatusEnum;
use Patoughi\Common\Enums\VersionTypeEnum;

#[OA\Schema(
    required: [
        'platform',
        'version',
        'type',
        'status',
        'build_number',
        'force_update',
    ],
    properties: [
        new OA\Property(property: 'platform', type: 'string', enum: VersionPlatformEnum::class),
        new OA\Property(property: 'version', type: 'string'),
        new OA\Property(property: 'type', type: 'string', enum: VersionTypeEnum::class),
        new OA\Property(property: 'status', type: 'string', enum: VersionStatusEnum::class),
        new OA\Property(property: 'build_number', type: 'string'),
        new OA\Property(property: 'force_update', type: 'string'),
    ],
)]
class VersionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'platform' => $this->platform,
            'version' => $this->version,
            'type' => $this->type,
            'status' => $this->status,
            'build_number' => $this->build_number,
            'force_update' => $this->force_update,
        ];
    }
}
