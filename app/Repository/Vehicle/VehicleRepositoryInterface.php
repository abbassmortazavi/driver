<?php

namespace App\Repository\Vehicle;

interface VehicleRepositoryInterface
{
    /**
     * @param  int  $driverId
     * @param  int  $vehicleId
     * @return mixed
     */
    public function getDriverVehicle(int $driverId, int $vehicleId): mixed;
}
