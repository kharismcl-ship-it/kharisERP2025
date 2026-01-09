<?php

namespace Modules\Hostels\Console\Commands;

use Illuminate\Console\Command;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Room;
use Modules\PaymentsChannel\Models\PayIntent;

class ReleaseExpiredManualBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hostels:release-expired-manual-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release room and bed bookings for manual payments not confirmed within 24 hours';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking for expired manual payment bookings...');

        // Find pending manual payments older than 24 hours
        $expiredIntents = PayIntent::where('provider', 'manual')
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        $releasedCount = 0;

        foreach ($expiredIntents as $intent) {
            try {
                // Update payment status to expired
                $intent->update(['status' => 'expired']);

                // Get the related booking
                $booking = $intent->payable;

                if ($booking && $booking instanceof Booking) {
                    // Cancel the booking
                    $booking->update(['status' => 'cancelled']);

                    // Release the bed
                    if ($booking->bed_id) {
                        $bed = Bed::find($booking->bed_id);
                        if ($bed) {
                            $bed->update(['status' => 'available']);
                            $this->info("Released bed #{$bed->bed_number} from expired booking #{$booking->booking_reference}");
                        }
                    }

                    // Check if room should be made available
                    if ($booking->room_id) {
                        $this->checkAndReleaseRoomIfEmpty($booking->room);
                    }

                    $releasedCount++;
                    $this->info("Cancelled expired booking #{$booking->booking_reference}");
                }
            } catch (\Exception $e) {
                $this->error("Error processing intent #{$intent->id}: ".$e->getMessage());
            }
        }

        $this->info("Released {$releasedCount} expired manual payment bookings.");

        return 0;
    }

    /**
     * Check if room should be made available when no active bookings remain
     *
     * @return void
     */
    protected function checkAndReleaseRoomIfEmpty(Room $room)
    {
        // Count active bookings for this room
        $activeBookings = $room->bookings()
            ->where('status', 'confirmed')
            ->orWhere('status', 'pending')
            ->count();

        if ($activeBookings === 0) {
            $room->update(['status' => 'available']);
            $this->info("Released room #{$room->room_number} as it has no active bookings");
        }
    }
}
