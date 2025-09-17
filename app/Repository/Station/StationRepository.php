<?php

namespace App\Repository\Station;

use App\Models\Station;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class StationRepository extends BaseRepository implements StationRepositoryInterface
{
    public function __construct(Station $model)
    {
        parent::__construct($model);
    }
}
