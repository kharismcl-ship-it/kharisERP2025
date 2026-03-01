<?php

namespace Modules\Hostels\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Hostels\Models\Booking;

class BookingCancelled
{
    use SerializesModels;

    public Booking $booking;

    public float $refundAmount;

    public function __construct(Booking $booking, float $refundAmount = 0.0)
    {
        $this->booking = $booking;
        $this->refundAmount = $refundAmount;
    }
}
