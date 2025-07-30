<?php

namespace App\Repository\Bid;

use App\Models\Bid;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class BidRepository extends BaseRepository implements BidRepositoryInterface
{
    public function __construct(Bid $model)
    {
        parent::__construct($model);
    }

    /**
     * @param  int|null  $driver_id
     * @param  int|null  $transport_id
     * @return bool
     */
    public function checkDriverBidTransport(?int $driver_id, ?int $transport_id): bool
    {
        return $this->model->query()->where(['driver_id' => $driver_id, 'transport_id' => $transport_id])->exists();
    }
}
