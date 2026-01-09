<?php

namespace Modules\Hostels\Http\Livewire;

use Livewire\Component;
use Modules\Hostels\Models\HostelOccupant;

class HostelOccupantList extends Component
{
    public function render()
    {
        return view('hostels::livewire.hostel-occupant-list', [
            'hostelOccupants' => HostelOccupant::where('hostel_id', request()->route('hostel'))->paginate(),
        ])->layout('layouts.app');
    }
}
