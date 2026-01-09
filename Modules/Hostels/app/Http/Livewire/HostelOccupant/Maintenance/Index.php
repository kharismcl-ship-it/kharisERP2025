<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Maintenance;

use Livewire\Component;
use Modules\Hostels\Models\MaintenanceRequest;

class Index extends Component
{
    public function __invoke()
    {
        return $this->render();
    }

    public function render()
    {
        $hostelOccupantId = auth('hostel_occupant')->user()->hostel_occupant_id;

        $requests = MaintenanceRequest::where('reported_by_hostel_occupant_id', $hostelOccupantId)
            ->with(['hostel', 'room'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hostels::livewire.hostel-occupant.maintenance.index', [
            'requests' => $requests,
        ])->layout('hostels::layouts.app');
    }
}
