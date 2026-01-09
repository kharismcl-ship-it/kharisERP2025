<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Hostels\Models\Booking;
use Tests\TestCase;

class BookingConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the active scope works correctly.
     *
     * @return void
     */
    public function test_active_scope_filters_correctly()
    {
        // Create active booking
        $activeBooking = Booking::create([
            'hostel_id' => 1,
            'room_id' => 1,
            'bed_id' => 1,
            'tenant_id' => 1,
            'booking_reference' => 'BK-001',
            'booking_type' => 'academic',
            'check_in_date' => now(),
            'check_out_date' => now()->addDays(30),
            'status' => 'pending', // Active status
            'total_amount' => 1000,
            'amount_paid' => 0,
            'balance_amount' => 1000,
            'payment_status' => 'unpaid',
            'channel' => 'online',
        ]);

        // Create inactive booking
        $inactiveBooking = Booking::create([
            'hostel_id' => 1,
            'room_id' => 1,
            'bed_id' => 1,
            'tenant_id' => 1,
            'booking_reference' => 'BK-002',
            'booking_type' => 'academic',
            'check_in_date' => now(),
            'check_out_date' => now()->addDays(30),
            'status' => 'cancelled', // Inactive status
            'total_amount' => 1000,
            'amount_paid' => 0,
            'balance_amount' => 1000,
            'payment_status' => 'unpaid',
            'channel' => 'online',
        ]);

        // Testactive scope
        $activeBookings = Booking::active()->get();
        $this->assertCount(1, $activeBookings);
        $this->assertEquals($activeBooking->id, $activeBookings->first()->id);

        // Test inactive scope
        $inactiveBookings = Booking::inactive()->get();
        $this->assertCount(1, $inactiveBookings);
        $this->assertEquals($inactiveBooking->id, $inactiveBookings->first()->id);
    }
}
