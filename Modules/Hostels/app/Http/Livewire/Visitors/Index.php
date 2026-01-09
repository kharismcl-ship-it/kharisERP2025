<?php

namespace Modules\Hostels\Http\Livewire\Visitors;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\VisitorLog;

class Index extends Component
{
    public Hostel $hostel;

    public $showCreateModal = false;

    public $showCheckOutModal = false;

    public $selectedHostelOccupantId;

    public $visitorName;

    public $visitorPhone;

    public $purpose;

    public $checkOutVisitorId;

    public $search = '';

    public $statusFilter = '';

    protected $rules = [
        'selectedHostelOccupantId' => 'required|exists:hostel_occupants,id',
        'visitorName' => 'required|string|max:255',
        'visitorPhone' => 'nullable|string|max:20',
        'purpose' => 'nullable|string|max:500',
    ];

    public function mount(Hostel $hostel)
    {
        $this->hostel = $hostel;
    }

    public function getVisitorLogsProperty()
    {
        $query = VisitorLog::where('hostel_id', $this->hostel->id)
            ->with(['tenant', 'recordedByUser']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('visitor_name', 'like', '%'.$this->search.'%')
                    ->orWhere('purpose', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->statusFilter === 'checked_in') {
            $query->whereNull('check_out_at');
        } elseif ($this->statusFilter === 'checked_out') {
            $query->whereNotNull('check_out_at');
        }

        return $query->orderBy('check_in_at', 'desc')->paginate(10);
    }

    public function getHostelOccupantsProperty()
    {
        return HostelOccupant::where('hostel_id', $this->hostel->id)
            ->where('status', 'active')
            ->get();
    }

    public function saveVisitorLog()
    {
        $this->validate();

        VisitorLog::create([
            'hostel_id' => $this->hostel->id,
            'hostel_occupant_id' => $this->selectedHostelOccupantId,
            'visitor_name' => $this->visitorName,
            'visitor_phone' => $this->visitorPhone,
            'purpose' => $this->purpose,
            'check_in_at' => now(),
            'recorded_by_user_id' => Auth::id(),
        ]);

        $this->resetForm();
        $this->showCreateModal = false;
        $this->dispatchBrowserEvent('notification', ['message' => 'Visitor checked in successfully!']);
    }

    public function checkOutVisitor(VisitorLog $visitorLog)
    {
        $visitorLog->update([
            'check_out_at' => now(),
        ]);

        $this->showCheckOutModal = false;
        $this->dispatchBrowserEvent('notification', ['message' => 'Visitor checked out successfully!']);
    }

    public function resetForm()
    {
        $this->selectedHostelOccupantId = null;
        $this->visitorName = '';
        $this->visitorPhone = '';
        $this->purpose = '';
    }

    public function render()
    {
        return view('hostels::livewire.visitors.index')
            ->layout('layouts.app');
    }
}
