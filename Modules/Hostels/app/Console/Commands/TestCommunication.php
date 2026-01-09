<?php

namespace Modules\Hostels\Console\Commands;

use Illuminate\Console\Command;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Services\HostelCommunicationService;

class TestCommunication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hostels:test-communication';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test communication features';

    /**
     * Execute the console command.
     */
    public function handle(HostelCommunicationService $communicationService)
    {
        $this->info('Testing communication features...');

        // Find a booking to test with
        $booking = Booking::with(['hostelOccupant', 'hostel', 'room', 'bed'])->first();

        if (! $booking) {
            $this->warn('No booking found to test with.');

            return;
        }

        if (! $booking->hostelOccupant) {
            $this->warn('Booking has no hostel occupant.');

            return;
        }

        $this->info("Testing with booking #{$booking->booking_reference}");

        // Test booking confirmation
        $this->info('Sending booking confirmation...');
        $communicationService->sendBookingConfirmation($booking);
        $this->info('Booking confirmation sent.');

        // Test check-in notification
        $this->info('Sending check-in notification...');
        $communicationService->sendCheckInNotification($booking);
        $this->info('Check-in notification sent.');

        // Test payment receipt
        $this->info('Sending payment receipt...');
        $communicationService->sendPaymentReceipt($booking, 100.00);
        $this->info('Payment receipt sent.');

        // Test checkout reminder
        $this->info('Sending checkout reminder...');
        $communicationService->sendCheckoutReminder($booking);
        $this->info('Checkout reminder sent.');

        $this->info('All communication tests completed.');
    }
}
