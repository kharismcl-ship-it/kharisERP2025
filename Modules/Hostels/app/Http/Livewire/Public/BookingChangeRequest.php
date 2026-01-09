<?php

namespace Modules\Hostels\Http\Livewire\Public;

use Livewire\Component;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\BookingChangeRequest as ChangeRequest;
use Modules\Hostels\Models\Room;

class BookingChangeRequest extends Component
{
    public Booking $booking;

    public $requestedRoomId;

    public $requestedBedId;

    public $reason;

    public $availableRooms = [];

    public $availableBeds = [];

    protected $rules = [
        'requestedRoomId' => 'required|exists:rooms,id',
        'requestedBedId' => 'nullable|exists:beds,id',
        'reason' => 'required|string|max:500',
    ];

    public function mount(Booking $booking)
    {
        $this->booking = $booking->load(['room', 'bed', 'hostel']);

        // Get available rooms in the same hostel
        $this->availableRooms = Room::where('hostel_id', $this->booking->hostel_id)
            ->where('status', 'available')
            ->get();

        // If a room is already selected, get its beds
        if ($this->booking->room_id) {
            $this->requestedRoomId = $this->booking->room_id;
            $this->updatedRequestedRoomId($this->booking->room_id);
        }
    }

    public function updatedRequestedRoomId($roomId)
    {
        $this->requestedBedId = null;
        $this->availableBeds = Bed::where('room_id', $roomId)
            ->where('status', 'available')
            ->get();
    }

    public function submit()
    {
        $this->validate();

        // Check if there's already a pending change request for this booking
        $existingRequest = ChangeRequest::where('booking_id', $this->booking->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            session()->flash('error', 'You already have a pending change request for this booking.');

            return;
        }

        // Check if the requested bed/room is the same as current
        if ($this->requestedRoomId == $this->booking->room_id &&
            $this->requestedBedId == $this->booking->bed_id) {
            session()->flash('error', 'The requested room/bed is the same as your current selection.');

            return;
        }

        // Create the change request
        ChangeRequest::create([
            'booking_id' => $this->booking->id,
            'requested_room_id' => $this->requestedRoomId,
            'requested_bed_id' => $this->requestedBedId,
            'reason' => $this->reason,
            'status' => 'pending',
        ]);

        session()->flash('message', 'Your change request has been submitted successfully and is pending approval.');
    }

    public function render()
    {
        return view('hostels::livewire.public.booking-change-request')
            ->layout('hostels::layouts.public');
    }
}
