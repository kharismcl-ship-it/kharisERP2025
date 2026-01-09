<?php

namespace Modules\Hostels\Http\Livewire;

use Livewire\Component;
use Modules\Hostels\Models\Hostel;

class Dashboard extends Component
{
    public Hostel $hostel;

    public function mount(Hostel $hostel)
    {
        $this->hostel = $hostel;
    }

    public function render()
    {
        return view('hostels::livewire.dashboard')
            ->layout('layouts.app');
    }
}
