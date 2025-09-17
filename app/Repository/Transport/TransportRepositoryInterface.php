<?php

namespace App\Repository\Transport;

interface TransportRepositoryInterface
{
    /**
     * @param  int  $transport_id
     * @return mixed
     */
    public function checkTransportStatus(int $transport_id): mixed;

    /**
     * @param  int  $driverId
     * @return mixed
     */
    public function getCurrentDriverTransport(int $driverId): mixed;
}
