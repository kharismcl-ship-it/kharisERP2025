<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Maintenance;

use Livewire\Component;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\MaintenanceRequest;
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
        $this->hostels = Hostel::where('status', 'active')->get();
    }

    public function updatedSelectedHostel($hostelId)
    {
        $this->rooms = Room::where('hostel_id', $hostelId)->get();
        $this->selectedRoom = null;
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
            ->layout('hostels::layouts.app');
    }
}
