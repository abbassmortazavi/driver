<?php

namespace App\Repository\Shipment;

use App\Models\Shipment;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class ShipmentRepository extends BaseRepository implements ShipmentRepositoryInterface
{
    public function __construct(Shipment $model)
    {
        parent::__construct($model);
    }
}
