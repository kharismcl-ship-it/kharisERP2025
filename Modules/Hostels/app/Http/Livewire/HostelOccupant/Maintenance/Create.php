<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Maintenance;

use Livewire\Component;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\MaintenanceRequest;
use Modules\Hostels\Models\Room;

class Create extends Component
{
    public $hostel = null;

    public $selectedHostel = null;

    public $selectedRoom = null;

    public $rooms = [];

    public $title;

    public $description;

    public $priority = 'medium';

    protected $rules = [
        'selectedHostel' => 'required',
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'priority' => 'required|in:low,medium,high,urgent',
    ];

    public function mount()
    {
        $occupant = auth('hostel_occupant')->user()->hostelOccupant;

        $this->hostel = Hostel::find($occupant->hostel_id);
        $this->selectedHostel = $occupant->hostel_id;

        $this->rooms = Room::where('hostel_id', $occupant->hostel_id)->get();

        // Pre-fill room from active booking if one exists
        $activeBooking = Booking::where('hostel_occupant_id', $occupant->id)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->latest()
            ->first();

        if ($activeBooking) {
            $this->selectedRoom = $activeBooking->room_id;
        }
    }

    public function createRequest()
    {
        $this->validate();

        $hostelOccupantId = auth('hostel_occupant')->user()->hostel_occupant_id;

        $request = MaintenanceRequest::create([
            'hostel_id' => $this->selectedHostel,
            'room_id' => $this->selectedRoom,
            'reported_by_hostel_occupant_id' => $hostelOccupantId,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => 'open',
            'reported_at' => now(),
        ]);

        session()->flash('message', 'Maintenance request submitted successfully.');

        return redirect()->route('hostel_occupant.maintenance.index');
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.maintenance.create')
            ->layout('hostels::layouts.occupant');
    }
}
