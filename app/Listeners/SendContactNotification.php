<?php

namespace App\Listeners;

use App\Events\Contacted;

class SendContactNotification
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
    public function handle(Contacted $event): void
    {
        $event->contact->messages()->create(['message' => $event->message]);
    }
}
