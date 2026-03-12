<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant;

use Livewire\Component;

class Navigation extends Component
{
    public function render()
    {
        $user     = auth('hostel_occupant')->user();
        $occupant = $user?->hostelOccupant;
        $hostel   = $occupant?->hostel;

        return view('hostels::components.hostel-occupant.navigation', [
            'hostel'   => $hostel,
            'occupant' => $occupant,
            'user'     => $user,
        ]);
    }
}
