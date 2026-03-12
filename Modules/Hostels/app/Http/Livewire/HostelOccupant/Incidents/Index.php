<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Incidents;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Hostels\Models\Incident;

class Index extends Component
{
    use WithPagination;

    public function getIncidentsProperty()
    {
        $occupantId = auth('hostel_occupant')->user()->hostel_occupant_id;

        return Incident::with(['hostel', 'room'])
            ->where('hostel_occupant_id', $occupantId)
            ->latest()
            ->paginate(15);
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.incidents.index', [
            'incidents' => $this->incidents,
        ])->layout('hostels::layouts.occupant');
    }
}
