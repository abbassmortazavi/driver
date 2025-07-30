<?php

namespace App\Repository\KycLog;

use App\Models\KycLog;
use Patoughi\Common\Enums\KycLogStatusEnum;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class KycLogRepository extends BaseRepository implements KycLogRepositoryInterface
{
    public function __construct(KycLog $model)
    {
        parent::__construct($model);
    }

    public function findByUserIdAndStatus(int $userId, KycLogStatusEnum $status): ?KycLog
    {
        return $this->model->query()
            ->where('user_id', $userId)
            ->where('status', $status)
            ->first();
    }

    public function findByUserIdAndTrackId(int $userId, string $trackId): ?KycLog
    {
        return $this->model->query()
            ->where('user_id', $userId)
            ->where('track_id', $trackId)
            ->first();
    }
}
