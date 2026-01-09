<?php

namespace Modules\Hostels\Console\Commands;

use Illuminate\Console\Command;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Services\HostelCommunicationService;

class SendPreArrivalReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hostels:send-pre-arrival-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send pre-arrival reminder notifications for confirmed bookings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $communicationService = app(HostelCommunicationService::class);

        // Get confirmed bookings with check-in dates in the future
        $bookings = Booking::where('status', 'confirmed')
            ->whereDate('check_in_date', '>', now())
            ->get();

        $sentCount = 0;
        $errorCount = 0;

        foreach ($bookings as $booking) {
            try {
                $daysUntilCheckIn = now()->diffInDays($booking->check_in_date, false);

                if ($daysUntilCheckIn === 3) {
                    // Send 3-day reminder
                    $communicationService->sendPreArrivalReminder($booking);
                    $sentCount++;
                    $this->info("Sent 3-day reminder for booking #{$booking->booking_reference}");
                } elseif ($daysUntilCheckIn === 1) {
                    // Send 1-day final reminder
                    $communicationService->sendPreArrivalFinalReminder($booking);
                    $sentCount++;
                    $this->info("Sent 1-day final reminder for booking #{$booking->booking_reference}");
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("Failed to send reminder for booking #{$booking->booking_reference}: ".$e->getMessage());
            }
        }

        $this->info("Pre-arrival reminders sent: {$sentCount}, errors: {$errorCount}");

        return Command::SUCCESS;
    }
}
