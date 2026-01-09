<?php

namespace Modules\Hostels\Listeners;

use Modules\Hostels\Events\BookingConfirmed;
use Modules\Hostels\Services\HostelCommunicationService;

class SendPreArrivalWelcome
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(BookingConfirmed $event)
    {
        $booking = $event->booking;

        // Send pre-arrival welcome communication immediately after booking confirmation
        $communicationService = app(HostelCommunicationService::class);
        $communicationService->sendPreArrivalWelcome($booking);
    }
}
