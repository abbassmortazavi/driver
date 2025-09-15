<?php

namespace App\Repository\DispatchUnitType;

use App\Models\DispatchUnitType;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class DispatchUnitTypeRepository extends BaseRepository implements DispatchUnitTypeRepositoryInterface
{
    public function __construct(DispatchUnitType $model)
    {
        parent::__construct($model);
    }
}
