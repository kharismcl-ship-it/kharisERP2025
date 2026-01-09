<?php

namespace Modules\Hostels\Listeners;

use Modules\Hostels\Events\BookingConfirmed;
use Modules\Hostels\Notifications\BookingConfirmationNotification;

class SendBookingConfirmation
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(BookingConfirmed $event)
    {
        $notification = new BookingConfirmationNotification;
        $notification->send($event->booking);
    }
}
