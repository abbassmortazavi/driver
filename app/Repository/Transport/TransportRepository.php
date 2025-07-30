<?php

namespace App\Repository\Transport;

use App\Models\Transport;
use Illuminate\Database\Eloquent\Collection;
use Patoughi\Common\Enums\BidStatusEnum;
use Patoughi\Common\Enums\TransportStatusEnum;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class TransportRepository extends BaseRepository implements TransportRepositoryInterface
{
    /**
     * @param  Transport  $model
     */
    public function __construct(Transport $model)
    {
        parent::__construct($model);
    }

    /**
     * @param  int  $transport_id
     * @return bool
     */
    public function checkTransportStatus(int $transport_id): bool
    {
        return $this->model->query()->where('transport_id', $transport_id)
            ->whereIn('status', [BidStatusEnum::REJECTED, BidStatusEnum::ACCEPTED])
            ->exists();
    }

    /**
     * @param  int  $driverId
     * @return Collection
     */
    public function getCurrentDriverTransport(int $driverId): Collection
    {
        return $this->model->query()
            ->with(['order', 'vehicle', 'order', 'shipments', 'vehicleType', 'waybills'])
            ->where('driver_id', $driverId)
            ->where('status', '=', TransportStatusEnum::IN_TRANSIT->value)
            ->get();
    }
}
