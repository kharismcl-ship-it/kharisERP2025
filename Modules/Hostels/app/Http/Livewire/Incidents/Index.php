<?php

namespace Modules\Hostels\Http\Livewire\Incidents;

use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\Incident;
use Modules\Hostels\Models\Room;

class Index extends Component
{
    public Hostel $hostel;

    public $showCreateModal = false;

    public $selectedRoomId;

    public $selectedHostelOccupantId;

    public $title;

    public $description;

    public $severity = 'minor';

    public $actionTaken;

    public $statusFilter = '';

    public $severityFilter = '';

    public $search = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'severity' => 'required|in:minor,major,critical',
        'selectedRoomId' => 'nullable|exists:rooms,id',
        'selectedHostelOccupantId' => 'nullable|exists:hostel_occupants,id',
        'actionTaken' => 'nullable|string',
    ];

    public function mount(Hostel $hostel)
    {
        $this->hostel = $hostel;
    }

    public function getIncidentsProperty()
    {
        $query = Incident::where('hostel_id', $this->hostel->id)
            ->with(['room', 'tenant', 'reportedByUser']);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->severityFilter) {
            $query->where('severity', $this->severityFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getAvailableRoomsProperty()
    {
        return Room::where('hostel_id', $this->hostel->id)->get();
    }

    public function getHostelOccupantsProperty()
    {
        return HostelOccupant::where('hostel_id', $this->hostel->id)->get();
    }

    public function getUsersProperty()
    {
        // In a real application, you might want to filter by hostel staff
        return ModelsUser::all();
    }

    public function saveIncident()
    {
        $this->validate();

        Incident::create([
            'hostel_id' => $this->hostel->id,
            'room_id' => $this->selectedRoomId,
            'hostel_occupant_id' => $this->selectedHostelOccupantId,
            'title' => $this->title,
            'description' => $this->description,
            'severity' => $this->severity,
            'action_taken' => $this->actionTaken,
            'status' => 'open',
            'reported_at' => now(),
            'reported_by_user_id' => Auth::id(),
        ]);

        $this->resetForm();
        $this->showCreateModal = false;
        $this->dispatchBrowserEvent('notification', ['message' => 'Incident report created successfully!']);
    }

    public function updateStatus(Incident $incident, $status)
    {
        $incident->update(['status' => $status]);

        if ($status === 'resolved') {
            $incident->update(['resolved_at' => now()]);
        }

        $this->dispatchBrowserEvent('notification', ['message' => 'Incident updated!']);
    }

    public function resetForm()
    {
        $this->selectedRoomId = null;
        $this->selectedHostelOccupantId = null;
        $this->title = '';
        $this->description = '';
        $this->severity = 'minor';
        $this->actionTaken = '';
    }

    public function render()
    {
        return view('hostels::livewire.incidents.index')
            ->layout('layouts.app');
    }
}
