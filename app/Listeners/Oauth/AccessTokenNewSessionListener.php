<?php

namespace App\Listeners\Oauth;

use Illuminate\Http\Request;
use Laravel\Passport\Events\AccessTokenCreated;

readonly class AccessTokenNewSessionListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected Request $request)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AccessTokenCreated $event): void
    {
        // Implementation of the listener
    }
}
