<?php

namespace App\Repository\VehicleType;

use App\Models\VehicleType;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class VehicleTypeRepository extends BaseRepository implements VehicleTypeRepositoryInterface
{
    public function __construct(VehicleType $model)
    {
        parent::__construct($model);
    }
}
