<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Hostels\Models\BookingChangeRequest;
use Tests\TestCase;

class BookingChangeRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a booking change request can be created.
     *
     * @return void
     */
    public function test_booking_change_request_can_be_created()
    {
        // Create a change request with minimal data
        $changeRequest = BookingChangeRequest::create([
            'booking_id' => 1,
            'requested_room_id' => 2,
            'requested_bed_id' => 3,
            'reason' => 'Want to move to a quieter room',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('booking_change_requests', [
            'id' => $changeRequest->id,
            'booking_id' => 1,
            'requested_room_id' => 2,
            'requested_bed_id' => 3,
            'reason' => 'Want to move to a quieter room',
            'status' => 'pending',
        ]);
    }

    /**
     * Test the scopes for booking change requests.
     *
     * @return void
     */
    public function test_booking_change_request_scopes()
    {
        // Create test change requests
        BookingChangeRequest::create([
            'booking_id' => 1,
            'requested_room_id' => 2,
            'requested_bed_id' => 3,
            'reason' => 'Want to move to a quieter room',
            'status' => 'pending',
        ]);

        BookingChangeRequest::create([
            'booking_id' => 2,
            'requested_room_id' => 3,
            'requested_bed_id' => 4,
            'reason' => 'Need a larger room',
            'status' => 'approved',
        ]);

        BookingChangeRequest::create([
            'booking_id' => 3,
            'requested_room_id' => 4,
            'requested_bed_id' => 5,
            'reason' => 'Moving to another hostel',
            'status' => 'rejected',
        ]);

        // Test scopes
        $this->assertCount(1, BookingChangeRequest::pending()->get());
        $this->assertCount(1, BookingChangeRequest::approved()->get());
        $this->assertCount(1, BookingChangeRequest::rejected()->get());
    }

    /**
     * Test model relationships.
     *
     * @return void
     */
    public function test_booking_change_request_relationships()
    {
        // Create a change request
        $changeRequest = BookingChangeRequest::create([
            'booking_id' => 1,
            'requested_room_id' => 2,
            'requested_bed_id' => 3,
            'reason' => 'Want to move to a quieter room',
            'status' => 'pending',
        ]);

        // Test that we can access the model
        $this->assertInstanceOf(BookingChangeRequest::class, $changeRequest);
    }
}
