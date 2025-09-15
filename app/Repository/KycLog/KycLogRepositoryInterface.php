<?php

namespace App\Repository\KycLog;

use App\Models\KycLog;
use Patoughi\Common\Enums\KycLogStatusEnum;

interface KycLogRepositoryInterface
{
    public function findByUserIdAndStatus(int $userId, KycLogStatusEnum $status): ?KycLog;

    public function findByUserIdAndTrackId(int $userId, string $trackId): ?KycLog;
}
