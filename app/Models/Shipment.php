<?php

namespace App\Models;

use App\Repository\Shipment\ShipmentRepository;
use Patoughi\Common\Orm\Attributes\Repository;
use Patoughi\Common\Orm\Models\Shipment as ShipmentModel;

#[Repository(repositoryClass: ShipmentRepository::class)]
class Shipment extends ShipmentModel {}
