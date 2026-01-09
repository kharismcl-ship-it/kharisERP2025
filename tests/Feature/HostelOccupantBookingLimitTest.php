<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\HostelOccupant;
use Tests\TestCase;

class HostelOccupantBookingLimitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the hostel occupant booking limit methods.
     *
     * @return void
     */
    public function test_hostel_occupant_booking_limit_methods()
    {
        // Create a hostel occupant
        $hostelOccupant = HostelOccupant::create([
            'hostel_id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'full_name' => 'John Doe',
            'gender' => 'male',
            'phone' => '1234567890',
            'email' => 'john.doe@example.com',
            'status' => 'prospect',
        ]);

        // Initially, hostel occupant should have 0 bookings
        $this->assertEquals(0, $hostelOccupant->getSemesterBookingCount('2023/2024', '1'));
        $this->assertFalse($hostelOccupant->hasReachedSemesterBookingLimit('2023/2024', '1'));

        // Create 3 bookings for the same semester
        for ($i = 1; $i <= 3; $i++) {
            Booking::create([
                'hostel_id' => 1,
                'room_id' => 1,
                'bed_id' => $i,
                'hostel_occupant_id' => $hostelOccupant->id,
                'booking_reference' => 'BK-00'.$i,
                'booking_type' => 'academic',
                'academic_year' => '2023/2024',
                'semester' => '1',
                'check_in_date' => now()->addDays(10),
                'check_out_date' => now()->addDays(40),
                'status' => 'confirmed',
                'total_amount' => 1000,
                'amount_paid' => 1000,
                'balance_amount' => 0,
                'payment_status' => 'paid',
                'channel' => 'online',
            ]);
        }

        // Check that hostel occupant has 3 bookings
        $this->assertEquals(3, $hostelOccupant->getSemesterBookingCount('2023/2024', '1'));
        $this->assertTrue($hostelOccupant->hasReachedSemesterBookingLimit('2023/2024', '1'));
    }

    /**
     * Test that cancelled bookings don't count toward the limit.
     *
     * @return void
     */
    public function test_cancelled_bookings_dont_count_toward_limit()
    {
        // Create a hostel occupant
        $hostelOccupant = HostelOccupant::create([
            'hostel_id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'full_name' => 'John Doe',
            'gender' => 'male',
            'phone' => '1234567890',
            'email' => 'john.doe@example.com',
            'status' => 'prospect',
        ]);

        // Create 2confirmed bookings and 1 cancelled booking
        for ($i = 1; $i <= 2; $i++) {
            Booking::create([
                'hostel_id' => 1,
                'room_id' => 1,
                'bed_id' => $i,
                'hostel_occupant_id' => $hostelOccupant->id,
                'booking_reference' => 'BK-00'.$i,
                'booking_type' => 'academic',
                'academic_year' => '2023/2024',
                'semester' => '1',
                'check_in_date' => now()->addDays(10),
                'check_out_date' => now()->addDays(40),
                'status' => 'confirmed',
                'total_amount' => 1000,
                'amount_paid' => 1000,
                'balance_amount' => 0,
                'payment_status' => 'paid',
                'channel' => 'online',
            ]);
        }

        Booking::create([
            'hostel_id' => 1,
            'room_id' => 1,
            'bed_id' => 3,
            'hostel_occupant_id' => $hostelOccupant->id,
            'booking_reference' => 'BK-003',
            'booking_type' => 'academic',
            'academic_year' => '2023/2024',
            'semester' => '1',
            'check_in_date' => now()->addDays(10),
            'check_out_date' => now()->addDays(40),
            'status' => 'cancelled',
            'total_amount' => 1000,
            'amount_paid' => 0,
            'balance_amount' => 1000,
            'payment_status' => 'unpaid',
            'channel' => 'online',
        ]);

        // Check that hostel occupant has only 2 bookings counting toward the limit
        $this->assertEquals(2, $hostelOccupant->getSemesterBookingCount('2023/2024', '1'));
        $this->assertFalse($hostelOccupant->hasReachedSemesterBookingLimit('2023/2024', '1'));
    }
}
