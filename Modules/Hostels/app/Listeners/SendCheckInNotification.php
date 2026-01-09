<?php

namespace Modules\Hostels\Listeners;

use Modules\Hostels\Events\TenantCheckedIn;
use Modules\Hostels\Notifications\CheckInNotification;

class SendCheckInNotification
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(TenantCheckedIn $event)
    {
        $notification = new CheckInNotification;
        $notification->send($event->booking);
    }
}
