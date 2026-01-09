<?php

namespace Modules\Hostels\Observers;

use Modules\Hostels\Models\Booking;
use Modules\Hostels\Services\HostelCommunicationService;

class BookingObserver
{
    protected $communicationService;

    public function __construct(HostelCommunicationService $communicationService)
    {
        $this->communicationService = $communicationService;
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // Check if the status was changed to 'checked_in'
        if ($booking->isDirty('status') && $booking->status === 'checked_in') {
            // The checkIn method will handle hostel occupant creation and status updates
            // This is just a fallback in case the method isn't called directly
            if (! $booking->hostel_occupant_id) {
                $booking->createHostelOccupantFromGuestInfo();
            }

            // Update actual check-in time if not already set
            if (! $booking->actual_check_in_at) {
                $booking->updateQuietly([
                    'actual_check_in_at' => now(),
                ]);
            }

            // Send check-in notification
            $this->communicationService->sendCheckInNotification($booking);
        }
    }
}
