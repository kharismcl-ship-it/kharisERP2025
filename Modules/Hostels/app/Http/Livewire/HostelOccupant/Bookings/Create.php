<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Bookings;

use Livewire\Component;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\FeeType;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\Room;

class Create extends Component
{
    public function __invoke()
    {
        return $this->render();
    }

    public $hostels;

    public $selectedHostel = null;

    public $selectedRoom = null;

    public $selectedBed = null;

    public $bookingType = 'academic';

    public $academicYear;

    public $semester;

    public $checkInDate;

    public $checkOutDate;

    public $rooms = [];

    public $beds = [];

    protected $rules = [
        'selectedHostel' => 'required',
        'selectedRoom' => 'required',
        'bookingType' => 'required|in:academic,short_stay',
        'academicYear' => 'nullable|required_if:bookingType,academic',
        'semester' => 'nullable|required_if:bookingType,academic',
        'checkInDate' => 'required|date',
        'checkOutDate' => 'required|date|after:checkInDate',
    ];

    public function mount()
    {
        $this->hostels = Hostel::where('status', 'active')->get();
        $this->academicYear = date('Y').'/'.(date('Y') + 1);
        $this->semester = '1';
        $this->checkInDate = date('Y-m-d');
        $this->checkOutDate = date('Y-m-d', strtotime('+1 year'));
    }

    public function updatedSelectedHostel($hostelId)
    {
        $this->rooms = Room::where('hostel_id', $hostelId)
            ->where('status', 'available')
            ->get();

        $this->selectedRoom = null;
        $this->beds = [];
        $this->selectedBed = null;
    }

    public function updatedSelectedRoom($roomId)
    {
        $this->beds = Bed::where('room_id', $roomId)
            ->where('status', 'available')
            ->get();

        $this->selectedBed = null;
    }

    public function createBooking()
    {
        $this->validate();

        $hostelOccupantId = auth('hostel_occupant')->user()->hostel_occupant_id;

        // Create the booking
        $booking = Booking::create([
            'hostel_id' => $this->selectedHostel,
            'room_id' => $this->selectedRoom,
            'bed_id' => $this->selectedBed,
            'hostel_occupant_id' => $hostelOccupantId,
            'booking_reference' => 'BK-'.time().'-'.rand(1000, 9999),
            'booking_type' => $this->bookingType,
            'academic_year' => $this->academicYear,
            'semester' => $this->semester,
            'check_in_date' => $this->checkInDate,
            'check_out_date' => $this->checkOutDate,
            'status' => 'pending',
            'total_amount' => 0, // Will be calculated
            'amount_paid' => 0,
            'balance_amount' => 0,
            'payment_status' => 'unpaid',
            'channel' => 'online',
        ]);

        // Calculate total amount based on room base rate and fees
        $room = Room::find($this->selectedRoom);
        $totalAmount = $room->base_rate;

        // Add mandatory fees
        $mandatoryFees = FeeType::where('hostel_id', $this->selectedHostel)
            ->where('is_mandatory', true)
            ->get();

        foreach ($mandatoryFees as $fee) {
            $totalAmount += $fee->default_amount;
        }

        // Update booking with calculated amount
        $booking->update([
            'total_amount' => $totalAmount,
            'balance_amount' => $totalAmount,
        ]);

        // Update bed status if selected
        if ($this->selectedBed) {
            $bed = Bed::find($this->selectedBed);
            $bed->update(['status' => 'reserved']);
        }

        return redirect()->route('hostel_occupant.bookings.show', $booking);
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.bookings.create')
            ->layout('hostels::layouts.app');
    }
}
