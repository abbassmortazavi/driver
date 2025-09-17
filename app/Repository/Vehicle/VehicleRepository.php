<?php

namespace App\Repository\Vehicle;

use App\Models\Vehicle;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class VehicleRepository extends BaseRepository implements VehicleRepositoryInterface
{
    public function __construct(Vehicle $model)
    {
        parent::__construct($model);
    }

    /**
     * @param  int  $driverId
     * @param  int  $vehicleId
     * @return mixed
     */
    public function getDriverVehicle(int $driverId, int $vehicleId): mixed
    {
        return $this->model->query()->where('driver_id', $driverId)->where('id', $vehicleId)->first();
    }
}
