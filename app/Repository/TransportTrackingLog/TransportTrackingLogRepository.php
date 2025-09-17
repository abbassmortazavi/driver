<?php

namespace App\Repository\TransportTrackingLog;

use App\Models\TransportTrackingLog;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class TransportTrackingLogRepository extends BaseRepository implements TransportTrackingLogRepositoryInterface
{
    /**
     * @param  TransportTrackingLog  $model
     */
    public function __construct(TransportTrackingLog $model)
    {
        parent::__construct($model);
    }
}
