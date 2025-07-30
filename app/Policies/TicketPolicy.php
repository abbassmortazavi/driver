<?php

namespace App\Policies;

use App\Models\PublicUser;
use App\Models\Ticket;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    public function CheckOwner(PublicUser $user, Ticket $ticket): Response
    {
        return $user->driver->getKey() == $ticket->user->driver->getKey()
            ? Response::allow()
            : Response::deny(trans('messages.you_do_not_own_this_ticket'));
    }
}
