<?php

namespace App\Http\Resources\Api\V1\User;

use App\Http\Resources\Api\V1\Driver\DriverResource;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\DriverBiometricStatusEnum;
use Patoughi\Common\Enums\DriverIdentityStatusEnum;
use Patoughi\Common\Enums\UserStatusEnum;

#[OA\Schema(
    required: [
        'id',
        'status',
        'name',
        'email',
        'created_at',
        'updated_at',
        'blocked_at',
        'driver_fulfillment_status'
    ],
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'status', type: 'string', enum: UserStatusEnum::class),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'phone_number', type: 'string'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'blocked_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'driver', ref: DriverResource::class, nullable: true),
        new OA\Property(
            property: 'driver_fulfillment_status',
            properties: [
                new OA\Property(
                    property: 'identity_status',
                    type: 'string',
                    enum: DriverIdentityStatusEnum::class
                ),
                new OA\Property(
                    property: 'biometric_status',
                    type: 'string',
                    enum: DriverBiometricStatusEnum::class
                )
            ],
            type: 'object',
            nullable: true
        ),    ],
)]
class PublicUserResource extends JsonResource
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
            'status' => $this->status,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'blocked_at' => $this->blocked_at,
            'driver' => DriverResource::make($this->whenLoaded('driver')),
            'driver_fulfillment_status' =>  $this->driverFulfillmentStatus(
                $this->whenLoaded('driver')
            ),
        ];
    }

    private function driverFulfillmentStatus(?Driver $driver): array
    {
        return [
            'identity_status' => $this->driverIdentityStatus($driver),
            'biometric_status' => $this->driverBiometricStatus($driver),
        ];
    }

    private function driverIdentityStatus(?Driver $driver): string
    {
        if($driver && $driver->identity_verified_at && $driver->profile_is_completed){
            return DriverIdentityStatusEnum::IDENTITY_VERIFIED->value;
        } else if ($driver && $driver->identity_verified_at && !$driver->profile_is_completed) {
            return DriverIdentityStatusEnum::NEED_To_COMPLETE_PROFILE->value;
        }

        return DriverIdentityStatusEnum::IDENTITY_UNVERIFIED->value;
    }

    private function driverBiometricStatus(?Driver $driver): string
    {
        if ($driver && $driver->identity_verified_at) {
            return DriverBiometricStatusEnum::BIOMETRIC_VERIFIED->value;
        }

        return DriverBiometricStatusEnum::BIOMETRIC_UNVERIFIED->value;
    }
}
