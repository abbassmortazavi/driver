<?php

namespace App\Repository\DriverVehicleAssignmentHistory;

use App\Models\DriverVehicleAssignmentHistory;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class DriverVehicleAssignmentHistoryRepository extends BaseRepository implements DriverVehicleAssignmentHistoryRepositoryInterface
{
    public function __construct(DriverVehicleAssignmentHistory $model)
    {
        parent::__construct($model);
    }
}
