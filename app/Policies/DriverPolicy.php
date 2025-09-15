<?php

namespace App\Policies;

use App\Models\Driver;
use App\Models\PublicUser;
use Patoughi\Common\Enums\DriverStatusEnum;
use Patoughi\Common\Enums\VehicleStatusEnum;

class DriverPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(PublicUser $publicUser, Driver $driver): bool
    {
        $condition = $driver->status == DriverStatusEnum::ACTIVE && ! is_null($driver->identity_verified_at);

        return $condition && $driver->vehicles()->where('status', VehicleStatusEnum::ACTIVE)->exists();
    }

    /**
     * @param  PublicUser  $user
     * @param  Driver  $driver
     * @return bool
     */
    public function owner(PublicUser $user, Driver $driver): bool
    {
        return $user->driver->getKey() == $driver->id;

    }
}
