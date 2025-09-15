<?php

namespace App\Policies;

use App\Models\PublicUser;
use App\Models\Vehicle;
use Patoughi\Common\Enums\VehicleStatusEnum;

class VehiclePolicy
{
    /**
     * @param  PublicUser  $publicUser
     * @param  Vehicle  $vehicle
     * @return bool
     */
    public function driverChangeStatus(PublicUser $publicUser, Vehicle $vehicle): bool
    {
        $driver = $publicUser->driver;

        return $driver->getKey() === $vehicle->driver_id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(PublicUser $publicUser, Vehicle $vehicle): bool
    {
        return $publicUser->driver->getKey() === $vehicle->driver_id;
    }

    /**
     * @param PublicUser $publicUser
     * @param Vehicle $vehicle
     * @return bool
     */
    public function update(PublicUser $publicUser, Vehicle $vehicle): bool
    {
        return $publicUser->driver->getKey() === $vehicle->driver_id;
    }
}
