<?php

namespace App\Http\Resources\Api\V1\Driver;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\DriverStatusEnum;
use Patoughi\Common\Enums\GenderEnum;

#[OA\Schema(
    required: [
        'id',
        'mdm_organization_id',
        'status',
        'first_name',
        'last_name',
        'national_code',
        'gender',
        'birth_date',
        'licence_number',
        'licence_type',
        'licence_expired_at',
        'hired_at',
        'emergency_contact_name',
        'emergency_contact_phone',
        'identity_verified_at',
        'biometric_verified_at',
        'activated_at',
        'deactivated_at',
    ],
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'mdm_organization_id', type: 'string'),
        new OA\Property(property: 'vehicle_type_id', type: 'integer'),
        new OA\Property(property: 'status', type: 'string', enum: DriverStatusEnum::class),
        new OA\Property(property: 'first_name', type: 'string'),
        new OA\Property(property: 'last_name', type: 'string'),
        new OA\Property(property: 'national_code', type: 'string'),
        new OA\Property(property: 'gender', type: 'string', enum: GenderEnum::class),
        new OA\Property(property: 'birth_date', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'licence_number', type: 'string'),
        new OA\Property(property: 'licence_type', type: 'string'),
        new OA\Property(property: 'licence_expired_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'hired_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'emergency_contact_name', type: 'string', nullable: true),
        new OA\Property(property: 'emergency_contact_phone', type: 'string', nullable: true),
    ],
)]
class DriverResource extends JsonResource
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
            'mdm_organization_id' => $this->mdm_organization_id ?? null,
            'first_name' => $this->first_name ?? null,
            'last_name' => $this->last_name ?? null,
            'national_code' => $this->national_code ?? null,
            'gender' => $this->gender ?? null,
            'birth_date' => $this->birth_date ?? null,
            'licence_number' => $this->licence_number ?? null,
            'licence_type' => $this->licence_type ?? null,
            'licence_expired_at' => $this->licence_expired_at ?? null,
            'hired_at' => $this->hiredAt ?? null,
            'emergency_contact_name' => $this->emergency_contact_name ?? null,
            'emergency_contact_phone' => $this->emergency_contact_phone ?? null,
        ];
    }
}
