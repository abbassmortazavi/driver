<?php

namespace App\Policies;

use App\Models\PublicUser;
use App\Models\Transport;
use Illuminate\Auth\Access\Response;
use Patoughi\Common\Orm\StatesMachine\Bid\State\BidPending;

class BidPolicy
{
    /**
     * @param  PublicUser  $user
     * @param  Transport  $transport
     * @return Response
     */
    public function checkExistsShipment(PublicUser $user, Transport $transport): Response
    {
        return $transport->shipments()->exists()
            ? Response::allow()
            : Response::deny(trans('messages.shipment_not_found'));
    }

    /**
     * @param  PublicUser  $user
     * @param  Transport  $transport
     * @return Response
     */
    public function checkExistsOrder(PublicUser $user, Transport $transport): Response
    {
        return $transport->shipments()->whereHas('order')->exists()
            ? Response::allow()
            : Response::deny(trans('messages.order_not_found'));
    }

    /**
     * @param  PublicUser  $user
     * @param  Transport  $transport
     * @return Response
     */
    public function allowPriceForNegotiation(PublicUser $user, Transport $transport): Response
    {
        return $transport->shipments()->whereHas('order', function ($query) {
            $query->where('allow_price_negotiation', true);
        })->exists()
            ? Response::allow()
            : Response::deny(trans('messages.you_can_not_do_this_bid'));
    }

    /**
     * @param  PublicUser  $user
     * @param  Bid  $bid
     * @return Response
     */
    public function cancel(PublicUser $user, Bid $bid): Response
    {
        if ($bid->driver_id !== $user->driver->getKey()) {
            return Response::deny(trans('messages.this_bid_does_not_belong_to_you'));
        }

        if (! $bid->status->canTransitionTo(BidPending::class)) {
            return Response::deny(trans('messages.only_pending_bids_can_be_cancelled'));
        }

        return Response::allow();
    }
}
