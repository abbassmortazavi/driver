<?php

namespace App\Repository\Driver;

use App\Models\Driver;

interface DriverRepositoryInterface
{
    /**
     * @param Driver $driver
     * @param array $attributes
     * @return mixed
     */
    public function driverListVehicles(Driver $driver, array $attributes): mixed;
}
