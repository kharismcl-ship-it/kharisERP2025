<?php

namespace Modules\Hostels\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use Modules\Hostels\Http\Livewire\Public\BookingWizard;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\Room;
use Tests\TestCase;

class BookingWizardPaymentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $hostel;

    protected $room;

    protected $bed;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup a test hostel with rooms and beds
        $this->hostel = Hostel::factory()->create([
            'name' => 'Test Hostel',
            'status' => 'active',
        ]);

        $this->room = Room::factory()->create([
            'hostel_id' => $this->hostel->id,
            'room_number' => 'Test Room',
            'max_occupancy' => 2,
            'current_occupancy' => 0,
        ]);

        $this->bed = Bed::factory()->create([
            'room_id' => $this->room->id,
            'bed_number' => 'A1',
            'status' => 'available',
        ]);
    }

    /** @test */
    public function it_can_complete_booking_with_payment_integration()
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();
        Log::shouldReceive('error')->zeroOrMoreTimes();

        Livewire::test(BookingWizard::class, ['hostel' => $this->hostel])
            ->set('step', 1)
            ->set('bookingType', 'academic')
            ->set('academicYear', '2024/2025')
            ->set('semester', '1')
            ->set('checkInDate', '2024-09-01')
            ->set('checkOutDate', '2024-12-15')
            ->call('nextStep')

            // Step 2: Guest information
            ->set('tenantType', 'new')
            ->set('firstName', 'John')
            ->set('lastName', 'Doe')
            ->set('email', 'john.doe@example.com')
            ->set('phone', '+233123456789')
            ->set('studentId', 'STU12345')
            ->set('institution', 'Test University')
            ->set('address', '123 Test Street')
            ->set('emergencyContactName', 'Jane Doe')
            ->set('emergencyContactPhone', '+233987654321')
            ->set('gender', 'male')
            ->set('dateOfBirth', '2000-01-01')
            ->call('nextStep')

            // Step 3: Room selection
            ->set('selectedRoom', $this->room->id)
            ->set('selectedBed', $this->bed->id)
            ->call('nextStep')

            // Step 4: Review and confirmation
            ->set('acceptTerms', true)
            ->call('nextStep')

            // Step 5: Payment
            ->set('paymentMethod', 'momo')
            ->call('processPayment')

            // Assertions
            ->assertHasNoErrors()
            ->assertRedirect(route('hostels.public.booking.confirmation', ['booking' => 1]));

        // Verify booking was created with correct status
        $booking = Booking::first();
        $this->assertNotNull($booking);
        $this->assertEquals('pending_approval', $booking->status);
        $this->assertEquals('paid', $booking->payment_status);
        $this->assertEquals('momo', $booking->payment_method);
        $this->assertNotNull($booking->payment_reference);
        $this->assertNotNull($booking->paid_at);

        // Verify bed status was updated
        $this->bed->refresh();
        $this->assertEquals('reserved_pending_approval', $this->bed->status);
    }

    /** @test */
    public function it_handles_payment_failure_gracefully()
    {
        // This test would simulate a payment failure scenario
        // In a real implementation, you would mock the payment gateway
        $this->markTestSkipped('Payment failure simulation requires payment gateway mocking');
    }

    /** @test */
    public function it_validates_payment_method_selection()
    {
        Livewire::test(BookingWizard::class, ['hostel' => $this->hostel])
            ->set('step', 5)
            ->set('paymentMethod', '') // Empty payment method
            ->call('processPayment')
            ->assertHasErrors(['paymentMethod' => 'required']);

        Livewire::test(BookingWizard::class, ['hostel' => $this->hostel])
            ->set('step', 5)
            ->set('paymentMethod', 'invalid') // Invalid payment method
            ->call('processPayment')
            ->assertHasErrors(['paymentMethod' => 'in']);
    }
}
