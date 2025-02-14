<?php

namespace App\Listeners;

use App\Events\NotificactionsAdminEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificactionsAdminListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NotificactionsAdminEvent  $event
     * @return void
     */
    public function handle(NotificactionsAdminEvent $event)
    {
        //
    }
}
