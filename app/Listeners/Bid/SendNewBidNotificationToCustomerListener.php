<?php

namespace App\Listeners\Bid;

use App\Notifications\NewBidNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Patoughi\Common\Events\Bid\BidCreated;

class SendNewBidNotificationToCustomerListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BidCreated $event): void
    {
        $bid = $event->bid;
        $transport = $bid->transport;
        $shipment = $transport->shipments()->whereHas('order')->firstOrFail();
        $order = $shipment->order;
        $customer = $order->customer;
        $user = $customer->user;

        $user->notify(new NewBidNotification(
            referenceNumber: $order->reference_number,
            transportId: $transport->id,
            bidId: $bid->id
        ));
    }
}
