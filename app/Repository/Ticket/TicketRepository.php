<?php

namespace App\Repository\Ticket;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Model;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class TicketRepository extends BaseRepository implements TicketRepositoryInterface
{
 public function __construct(Ticket $model)
 {
     parent::__construct($model);
 }
}
