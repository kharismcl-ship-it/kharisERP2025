<?php

namespace Modules\Hostels\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Hostels\Models\Booking;

class HostelOccupantCheckedIn
{
    use SerializesModels;

    public Booking $booking;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }
}
