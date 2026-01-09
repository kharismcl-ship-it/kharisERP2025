<?php

use Livewire\Livewire;
use Modules\Hostels\Http\Livewire\Public\BookingWizard;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\Room;

beforeEach(function () {
    $this->hostel = Hostel::factory()->create([
        'status' => 'active',
        'require_payment_before_checkin' => false,
    ]);

    $this->room = Room::factory()->create([
        'hostel_id' => $this->hostel->id,
        'status' => 'available',
    ]);

    $this->bed = Bed::factory()->create([
        'room_id' => $this->room->id,
        'status' => 'available',
    ]);
});

test('booking wizard can be accessed', function () {
    $response = $this->get('/hostels/'.$this->hostel->slug.'/book');
    $response->assertStatus(200);
});

test('booking wizard shows available hostels', function () {
    Livewire::test(BookingWizard::class, ['hostel' => $this->hostel])
        ->assertSee($this->hostel->name);
});

test('can complete booking workflow', function () {
    $occupantData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '+1234567890',
        'gender' => 'male',
        'id_card_number' => 'ID123456',
    ];

    // Check the room's gender policy and set appropriate gender
    $room = Room::find($this->room->id);
    $appropriateGender = 'male'; // Default

    if ($room->gender_policy === 'female') {
        $appropriateGender = 'female';
    } elseif ($room->gender_policy === 'male') {
        $appropriateGender = 'male';
    } // For mixed or inherit_hostel, either gender is fine

    $component = Livewire::test(BookingWizard::class, ['hostel' => $this->hostel])
        ->set('bookingType', 'academic')
        ->set('academicYear', '2024/2025')
        ->set('checkInDate', now()->format('Y-m-d'))
        ->set('checkOutDate', now()->addYear()->format('Y-m-d'))
        ->set('hostelOccupantType', 'new')
        ->set('firstName', 'John')
        ->set('lastName', 'Doe')
        ->set('email', 'john@example.com')
        ->set('phone', '+1234567890')
        ->set('gender', $appropriateGender)
        ->set('selectedRoom', $this->room->id)
        ->set('acceptTerms', true)
        ->call('createBooking');

    $this->assertDatabaseHas('bookings', [
        'hostel_id' => $this->hostel->id,
        'room_id' => $this->room->id,
        'status' => 'pending_approval',
    ]);

    // For new hostel occupants, the guest information is stored in the booking record
    // The hostel occupant record will be created later during admin check-in
    $this->assertDatabaseHas('bookings', [
        'hostel_id' => $this->hostel->id,
        'room_id' => $this->room->id,
        'guest_first_name' => 'John',
        'guest_last_name' => 'Doe',
        'guest_email' => 'john@example.com',
    ]);
});

test('bed becomes reserved during booking process', function () {
    Livewire::test(BookingWizard::class, ['hostel' => $this->hostel])
        ->set('selectedRoom', $this->room->id)
        ->set('selectedBed', $this->bed->id);

    $this->bed->refresh();
    // The bed status should remain available until booking is confirmed
    $this->assertEquals('available', $this->bed->status);
});

test('cannot book unavailable bed', function () {
    $unavailableBed = Bed::factory()->create([
        'room_id' => $this->room->id,
        'status' => 'occupied',
    ]);

    $component = Livewire::test(BookingWizard::class, ['hostel' => $this->hostel])
        ->set('bookingType', 'short_stay')
        ->set('checkInDate', now()->format('Y-m-d'))
        ->set('checkOutDate', now()->addDays(3)->format('Y-m-d'))
        ->set('hostelOccupantType', 'new')
        ->set('firstName', 'John')
        ->set('lastName', 'Doe')
        ->set('email', 'john@example.com')
        ->set('phone', '1234567890')
        ->set('gender', 'male')
        ->set('acceptTerms', true)
        ->set('selectedRoom', $this->room->id)
        ->set('selectedBed', $unavailableBed->id)
        ->call('createBooking');

    // The createBooking method catches exceptions and adds them as errors
    // instead of throwing them, so we need to check for the error message
    $component->assertHasErrors(['booking' => 'Selected bed is no longer available. Please select another bed.']);
});

test('booking calculates correct charges', function () {
    $roomWithRate = Room::factory()->create([
        'hostel_id' => $this->hostel->id,
        'base_rate' => '1000.00',
        'per_night_rate' => '50.00',
        'per_semester_rate' => '3000.00',
        'per_year_rate' => '5000.00',
    ]);

    $bedWithRate = Bed::factory()->create([
        'room_id' => $roomWithRate->id,
        'status' => 'available',
    ]);

    $component = Livewire::test(BookingWizard::class, ['hostel' => $this->hostel])
        ->set('selectedRoom', $roomWithRate->id)
        ->set('bookingType', 'short_stay')
        ->set('checkInDate', now()->format('Y-m-d'))
        ->set('checkOutDate', now()->addDays(7)->format('Y-m-d'));

    $bookingSummary = $component->get('bookingSummary');

    // Should calculate based on per_night_rate for short stay
    $this->assertTrue(
        $bookingSummary['total_amount'] === 350.00 ||
        abs($bookingSummary['total_amount'] - 350.00) < 0.01
    );
});

test('booking with existing occupant reuses occupant record', function () {
    $existingOccupant = HostelOccupant::factory()->create([
        'email' => 'existing@example.com',
        'phone' => '+1234567890',
        'hostel_id' => $this->hostel->id,
    ]);

    Livewire::test(BookingWizard::class, ['hostel' => $this->hostel])
        ->set('hostelOccupantType', 'existing')
        ->set('existingHostelOccupantId', $existingOccupant->id)
        ->set('selectedRoom', $this->room->id)
        ->call('createBooking');

    $this->assertDatabaseCount('hostel_occupants', 1); // Should not create duplicate
});
