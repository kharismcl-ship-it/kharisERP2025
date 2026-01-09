<?php

namespace Modules\Hostels\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Hostels\Models\Booking;

class ReleaseExpiredBookingHolds implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all bookings with expired holds
        $expiredBookings = Booking::withExpiredHold()
            ->with(['bed'])
            ->get();

        foreach ($expiredBookings as $booking) {
            try {
                // Release the bed and update booking status
                $booking->releaseBedIfHoldExpired();

                // Log the action
                Log::info('Released expired booking hold', [
                    'booking_id' => $booking->id,
                    'booking_reference' => $booking->booking_reference,
                    'bed_id' => $booking->bed_id,
                    'hold_expires_at' => $booking->hold_expires_at,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to release expired booking hold', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
