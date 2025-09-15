<?php

namespace App\Repository\ShipmentStatusHistory;

use App\Models\ShipmentStatusHistory;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class ShipmentStatusHistoryRepository extends BaseRepository implements ShipmentStatusHistoryRepositoryInterface
{
    public function __construct(ShipmentStatusHistory $model)
    {
        parent::__construct($model);
    }
}
