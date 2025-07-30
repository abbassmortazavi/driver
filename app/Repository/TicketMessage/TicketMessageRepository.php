<?php

namespace App\Repository\TicketMessage;

use App\Models\TicketMessage;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class TicketMessageRepository extends BaseRepository implements TicketMessageRepositoryInterface
{
    public function __construct(TicketMessage $model)
    {
        parent::__construct($model);
    }
}
