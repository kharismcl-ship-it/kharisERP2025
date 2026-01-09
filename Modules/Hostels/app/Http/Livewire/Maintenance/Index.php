<?php

namespace Modules\Hostels\Http\Livewire\Maintenance;

use Livewire\Component;
use Modules\Core\Models\User;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\MaintenanceRequest;
use Modules\Hostels\Models\Room;

class Index extends Component
{
    public Hostel $hostel;

    public $showCreateModal = false;

    public $selectedRoomId;

    public $selectedBedId;

    public $title;

    public $description;

    public $priority = 'medium';

    public $assignedToUserId;

    public $statusFilter = '';

    public $priorityFilter = '';

    public $search = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'priority' => 'required|in:low,medium,high,urgent',
        'selectedRoomId' => 'nullable|exists:rooms,id',
        'selectedBedId' => 'nullable|exists:beds,id',
        'assignedToUserId' => 'nullable|exists:users,id',
    ];

    public function mount(Hostel $hostel)
    {
        $this->hostel = $hostel;
    }

    public function getMaintenanceRequestsProperty()
    {
        $query = MaintenanceRequest::where('hostel_id', $this->hostel->id)
            ->with(['room', 'bed', 'assignedToUser']);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
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

    public function getBedsForRoomProperty()
    {
        if (! $this->selectedRoomId) {
            return collect();
        }

        return Bed::where('room_id', $this->selectedRoomId)->get();
    }

    public function getUsersProperty()
    {
        // In a real application, you might want to filter by hostel staff
        return User::all();
    }

    public function saveMaintenanceRequest()
    {
        $this->validate();

        MaintenanceRequest::create([
            'hostel_id' => $this->hostel->id,
            'room_id' => $this->selectedRoomId,
            'bed_id' => $this->selectedBedId,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => 'open',
            'assigned_to_user_id' => $this->assignedToUserId,
            'reported_at' => now(),
            'reported_by_user_id' => auth()->id(),
        ]);

        $this->resetForm();
        $this->showCreateModal = false;
        $this->dispatchBrowserEvent('notification', ['message' => 'Maintenance request created successfully!']);
    }

    public function updateStatus(MaintenanceRequest $request, $status)
    {
        $request->update(['status' => $status]);

        if ($status === 'completed') {
            $request->update(['completed_at' => now()]);
        }

        $this->dispatchBrowserEvent('notification', ['message' => 'Maintenance request updated!']);
    }

    public function resetForm()
    {
        $this->selectedRoomId = null;
        $this->selectedBedId = null;
        $this->title = '';
        $this->description = '';
        $this->priority = 'medium';
        $this->assignedToUserId = null;
    }

    public function render()
    {
        return view('hostels::livewire.maintenance.index')
            ->layout('layouts.app');
    }
}
