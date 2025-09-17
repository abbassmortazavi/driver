<?php

namespace App\Repository\DriverVehicleAssignment;

use App\Models\DriverVehicleAssignmentHistory;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class DriverVehicleAssignmentRepository extends BaseRepository implements DriverVehicleAssignmentRepositoryInterface
{
    public function __construct(DriverVehicleAssignmentHistory $model)
    {
        parent::__construct($model);
    }

    public function findByDriverVehicleId(int $driverId, int $vehicleId): mixed
    {
        return $this->model->query()->where('vehicle_id', $vehicleId)->where('driver_id', $driverId)->first();
    }
}
