<?php

namespace App\Models;

use App\Repository\ShipmentStatusHistory\ShipmentStatusHistoryRepository;
use Patoughi\Common\Orm\Attributes\Repository;
use Patoughi\Common\Orm\Models\ShipmentStatusHistory as ShipmentStatusHistoryModel;

#[Repository(repositoryClass: ShipmentStatusHistoryRepository::class)]
class ShipmentStatusHistory extends ShipmentStatusHistoryModel {}
