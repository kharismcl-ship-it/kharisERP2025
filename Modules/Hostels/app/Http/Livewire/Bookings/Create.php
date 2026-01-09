<?php

namespace Modules\Hostels\Http\Livewire\Bookings;

use Livewire\Component;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\BookingCharge;
use Modules\Hostels\Models\FeeType;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\Room;

class Create extends Component
{
    public Hostel $hostel;

    public $step = 1;

    // Step 1: Hostel Occupant selection
    public $hostelOccupantSearch = '';

    public $hostelOccupant;

    public $showCreateHostelOccupantForm = false;

    public $newHostelOccupant = [
        'first_name' => '',
        'last_name' => '',
        'other_names' => '',
        'gender' => '',
        'phone' => '',
        'email' => '',
        'student_id' => '',
    ];

    // Step 2: Booking type and period
    public $bookingType = 'academic';

    public $academicYear;

    public $semester;

    public $checkInDate;

    public $checkOutDate;

    // Step 3: Room and bed selection
    public $selectedRoom;

    public $selectedBed;

    // Step 4: Charges
    public $charges = [];

    public $additionalCharges = [];

    protected $rules = [
        // Hostel Occupant validation
        'newHostelOccupant.first_name' => 'required|string|max:255',
        'newHostelOccupant.last_name' => 'required|string|max:255',
        'newHostelOccupant.gender' => 'required|in:male,female',
        'newHostelOccupant.phone' => 'required|string|max:20',
        'newHostelOccupant.email' => 'nullable|email|max:255',
        'newHostelOccupant.student_id' => 'nullable|string|max:50',

        // Booking validation
        'bookingType' => 'required|in:academic,short_stay',
        'academicYear' => 'required_if:bookingType,academic|string|max:20',
        'semester' => 'required_if:bookingType,academic|string|max:20',
        'checkInDate' => 'required|date',
        'checkOutDate' => 'required|date|after:checkInDate',

        // Room/Bed selection
        'selectedRoom' => 'required',
        'selectedBed' => 'nullable',
    ];

    protected $messages = [
        'newHostelOccupant.first_name.required' => 'First name is required',
        'newHostelOccupant.last_name.required' => 'Last name is required',
        'newHostelOccupant.gender.required' => 'Gender is required',
        'newHostelOccupant.phone.required' => 'Phone number is required',
        'checkOutDate.after' => 'Check-out date must be after check-in date',
        'selectedRoom.required' => 'Please select a room',
    ];

    public function mount(Hostel $hostel)
    {
        $this->hostel = $hostel;

        // Set default dates
        $this->checkInDate = now()->format('Y-m-d');
        $this->checkOutDate = now()->addYear()->format('Y-m-d');

        // Handle pre-filled data from query parameters
        if (request()->has('preSelectedRoomId')) {
            $this->selectedRoom = request('preSelectedRoomId');
            $this->step = 3; // Skip to room selection step
        }

        if (request()->has('preSelectedBedId')) {
            $this->selectedBed = request('preSelectedBedId');
        }

        if (request()->has('preSelectedCheckInDate')) {
            $this->checkInDate = request('preSelectedCheckInDate');
        }

        if (request()->has('preSelectedCheckOutDate')) {
            $this->checkOutDate = request('preSelectedCheckOutDate');
        }
    }

    public function getHostelOccupantsProperty()
    {
        if (empty($this->hostelOccupantSearch)) {
            return HostelOccupant::where('hostel_id', $this->hostel->id)
                ->where('status', 'active')
                ->get();
        }

        return HostelOccupant::where('hostel_id', $this->hostel->id)
            ->where(function ($query) {
                $query->where('first_name', 'like', '%'.$this->hostelOccupantSearch.'%')
                    ->orWhere('last_name', 'like', '%'.$this->hostelOccupantSearch.'%')
                    ->orWhere('phone', 'like', '%'.$this->hostelOccupantSearch.'%');
            })
            ->where('status', 'active')
            ->get();
    }

    public function selectHostelOccupant($hostelOccupantId)
    {
        $this->hostelOccupant = HostelOccupant::find($hostelOccupantId);
        $this->hostelOccupantSearch = $this->hostelOccupant->first_name.' '.$this->hostelOccupant->last_name;
        $this->nextStep();
    }

    public function createHostelOccupant()
    {
        $this->validateOnly('newHostelOccupant.first_name');
        $this->validateOnly('newHostelOccupant.last_name');
        $this->validateOnly('newHostelOccupant.gender');
        $this->validateOnly('newHostelOccupant.phone');

        $this->newHostelOccupant['full_name'] = $this->newHostelOccupant['first_name'].' '.$this->newHostelOccupant['last_name'];
        $this->newHostelOccupant['hostel_id'] = $this->hostel->id;
        $this->newHostelOccupant['status'] = 'prospect';

        $this->hostelOccupant = HostelOccupant::create($this->newHostelOccupant);
        $this->hostelOccupantSearch = $this->hostelOccupant->first_name.' '.$this->hostelOccupant->last_name;
        $this->showCreateHostelOccupantForm = false;
        $this->nextStep();
    }

    public function nextStep()
    {
        if ($this->step === 1 && ! $this->hostelOccupant) {
            $this->addError('hostelOccupant', 'Please select or create a hostel occupant');

            return;
        }

        if ($this->step === 2) {
            $this->validateOnly('bookingType');
            if ($this->bookingType === 'academic') {
                $this->validateOnly('academicYear');
                $this->validateOnly('semester');
            }
            $this->validateOnly('checkInDate');
            $this->validateOnly('checkOutDate');
        }

        if ($this->step === 3) {
            $this->validateOnly('selectedRoom');
        }

        $this->step++;
    }

    public function previousStep()
    {
        $this->step--;
    }

    public function getRoomsProperty()
    {
        return Room::where('hostel_id', $this->hostel->id)
            ->with('beds')
            ->get();
    }

    public function selectRoom($roomId)
    {
        $this->selectedRoom = $roomId;
        $this->selectedBed = null;
    }

    public function selectBed($bedId)
    {
        $this->selectedBed = $bedId;
    }

    public function getSelectedRoomProperty()
    {
        if (! $this->selectedRoom) {
            return null;
        }

        return Room::find($this->selectedRoom);
    }

    public function getSelectedBedProperty()
    {
        if (! $this->selectedBed) {
            return null;
        }

        return Bed::find($this->selectedBed);
    }

    public function calculateCharges()
    {
        $this->charges = [];

        // Base rent calculation
        if ($this->selectedRoom) {
            $room = $this->selectedRoom;
            $days = \Carbon\Carbon::parse($this->checkInDate)->diffInDays($this->checkOutDate);

            $baseCharge = [
                'description' => 'Base Rent',
                'quantity' => 1,
                'unit_price' => $room->base_rate,
                'amount' => $room->base_rate,
            ];

            // Adjust based on billing cycle
            switch ($room->billing_cycle) {
                case 'per_night':
                    $baseCharge['quantity'] = $days;
                    $baseCharge['amount'] = $room->base_rate * $days;
                    break;
                case 'per_semester':
                    // For academic bookings, this is correct
                    break;
                case 'per_year':
                    // For academic year bookings, this is correct
                    break;
            }

            $this->charges[] = $baseCharge;
        }

        // Mandatory fees
        $mandatoryFees = FeeType::where('hostel_id', $this->hostel->id)
            ->where('is_mandatory', true)
            ->where('is_active', true)
            ->get();

        foreach ($mandatoryFees as $fee) {
            $this->charges[] = [
                'fee_type_id' => $fee->id,
                'description' => $fee->name,
                'quantity' => 1,
                'unit_price' => $fee->default_amount,
                'amount' => $fee->default_amount,
            ];
        }

        // Additional charges
        foreach ($this->additionalCharges as $charge) {
            $this->charges[] = $charge;
        }
    }

    public function addAdditionalCharge()
    {
        $this->additionalCharges[] = [
            'description' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'amount' => 0,
        ];
    }

    public function removeAdditionalCharge($index)
    {
        unset($this->additionalCharges[$index]);
        $this->additionalCharges = array_values($this->additionalCharges);
    }

    public function getTotalAmountProperty()
    {
        return collect($this->charges)->sum('amount');
    }

    public function saveBooking()
    {
        // Validate all steps
        $this->validate();

        // Calculate charges
        $this->calculateCharges();

        // Create booking
        $bookingData = [
            'hostel_id' => $this->hostel->id,
            'room_id' => $this->selectedRoom,
            'bed_id' => $this->selectedBed,
            'hostel_occupant_id' => $this->hostelOccupant->id,
            'booking_reference' => 'BK-'.now()->format('Ymd').'-'.strtoupper(\Illuminate\Support\Str::random(4)),
            'booking_type' => $this->bookingType,
            'academic_year' => $this->bookingType === 'academic' ? $this->academicYear : null,
            'semester' => $this->bookingType === 'academic' ? $this->semester : null,
            'check_in_date' => $this->checkInDate,
            'check_out_date' => $this->checkOutDate,
            'status' => 'pending',
            'total_amount' => $this->totalAmount,
            'amount_paid' => 0,
            'balance_amount' => $this->totalAmount,
            'payment_status' => 'unpaid',
            'channel' => 'walk_in',
        ];

        $booking = Booking::create($bookingData);

        // Create booking charges
        foreach ($this->charges as $charge) {
            $chargeData = array_merge($charge, ['booking_id' => $booking->id]);
            BookingCharge::create($chargeData);
        }

        // Update bed status if selected
        if ($this->selectedBed) {
            $bed = Bed::find($this->selectedBed);
            $bed->update(['status' => 'reserved']);
        }

        // Update room occupancy
        $room = Room::find($this->selectedRoom);
        $room->increment('current_occupancy');

        // Redirect to booking show page
        return redirect()->route('hostels.bookings.show', [$this->hostel, $booking]);
    }

    public function render()
    {
        return view('hostels::livewire.bookings.create')
            ->layout('layouts.app');
    }
}
