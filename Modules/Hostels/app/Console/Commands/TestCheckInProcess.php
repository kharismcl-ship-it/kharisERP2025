<?php

namespace Modules\Hostels\Console\Commands;

use Illuminate\Console\Command;
use Modules\Hostels\Models\Booking;

class TestCheckInProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hostels:test-check-in';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the check-in process for creating hostel occupant records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing check-in process...');

        // Find a confirmed booking without a hostel occupant
        $booking = Booking::where('status', 'confirmed')
            ->whereNull('hostel_occupant_id')
            ->first();

        if (! $booking) {
            $this->warn('No confirmed booking without tenant found.');

            return;
        }

        $this->info("Checking in booking #{$booking->booking_reference}");

        // Fill in some guest information for testing
        $booking->update([
            'guest_first_name' => 'John',
            'guest_last_name' => 'Doe',
            'guest_email' => 'john.doe@example.com',
            'guest_phone' => '+1234567890',
        ]);

        // Perform check-in
        $booking->checkIn();

        $this->info('Booking checked in successfully!');
        $this->info("Hostel occupant created with ID: {$booking->hostel_occupant_id}");
        $this->info("Hostel occupant name: {$booking->hostelOccupant->full_name}");
    }
}
