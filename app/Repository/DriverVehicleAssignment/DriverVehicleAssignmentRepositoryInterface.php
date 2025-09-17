<?php

namespace App\Repository\DriverVehicleAssignment;

interface DriverVehicleAssignmentRepositoryInterface
{
    public function findByDriverVehicleId(int $driverId, int $vehicleId): mixed;
}
