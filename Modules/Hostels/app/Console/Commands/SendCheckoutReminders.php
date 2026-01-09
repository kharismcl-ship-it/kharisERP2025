<?php

namespace Modules\Hostels\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Services\HostelCommunicationService;

class SendCheckoutReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hostels:send-checkout-reminders {--days=1 : Number of days before checkout to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send checkout reminders to tenants';

    /**
     * Execute the console command.
     */
    public function handle(HostelCommunicationService $communicationService)
    {
        $days = $this->option('days');
        $this->info("Sending checkout reminders for bookings ending in {$days} day(s)...");

        // Find bookings ending in the specified number of days
        $targetDate = Carbon::today()->addDays($days);

        $bookings = Booking::with(['tenant', 'hostel', 'room'])
            ->whereDate('check_out_date', $targetDate)
            ->where('status', 'checked_in')
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No bookings found for checkout reminders.');

            return;
        }

        $this->info("Found {$bookings->count()} bookings for checkout reminders.");

        $bar = $this->output->createProgressBar($bookings->count());
        $bar->start();

        foreach ($bookings as $booking) {
            try {
                $communicationService->sendCheckoutReminder($booking);
            } catch (\Exception $e) {
                $this->error("Failed to send checkout reminder for booking {$booking->booking_reference}: ".$e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info('Checkout reminders sent.');
    }
}
