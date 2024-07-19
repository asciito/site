<?php

namespace App\Listeners;

use App\Events\Contacted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        $event->user->messages()->create(['message' => $event->message]);
    }
}
