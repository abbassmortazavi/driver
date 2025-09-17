<?php

namespace App\Policies;

use App\Models\PublicUser;
use App\Models\Transport;
use Illuminate\Auth\Access\Response;
use Patoughi\Common\Enums\TransportAssignmentStatusEnum;
use Patoughi\Common\Enums\TransportStatusEnum;

class TransportPolicy
{
    /**
     * @param  PublicUser  $publicUser
     * @param  Transport  $transport
     * @return Response
     */
    public function checkTransportStatus(PublicUser $publicUser, Transport $transport): Response
    {
        return $transport
            ->where('assignment_status', TransportAssignmentStatusEnum::ASSIGNED->value)
            ->whereIn('status', [
                TransportStatusEnum::IN_TRANSIT->value,
                TransportStatusEnum::PASSED_STOPOVER->value,
                TransportStatusEnum::DELIVERED->value,
                TransportStatusEnum::COMPLETED->value,
                TransportStatusEnum::EXCEPTION->value,
                TransportStatusEnum::INVESTIGATE_INCOMPLETE_DELIVERY->value,
            ])
            ->exists()
            ? Response::allow()
            : Response::deny(trans('messages.transport_has_not_validate_status'));
    }

    /**
     * @param  PublicUser  $user
     * @param  Transport  $transport
     * @return Response
     */
    public function hasBidForCurrentDriver(PublicUser $user, Transport $transport): Response
    {
        return ! $transport->bids()->where('driver_id', $user->driver->id)->exists()
            ? Response::allow()
            : Response::deny(__('You had been bid before for this transport'));
    }

    /**
     * @param  PublicUser  $user
     * @param  Transport  $transport
     * @return bool
     */
    public function view(PublicUser $user, Transport $transport): bool
    {
        return $user->driver->getKey() === $transport->driver_id;
    }
}
