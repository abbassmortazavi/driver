<?php

namespace App\Repository\Bid;

interface BidRepositoryInterface
{
    /**
     * @param  int|null  $driver_id
     * @param  int|null  $transport_id
     * @return mixed
     */
    public function checkDriverBidTransport(?int $driver_id, ?int $transport_id): mixed;
}
