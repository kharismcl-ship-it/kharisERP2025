<?php

namespace Modules\Hostels\Http\Livewire\Admin;

use Livewire\Component;
use Modules\Hostels\Models\Hostel;

class RoomList extends Component
{
    public Hostel $hostel;

    public function mount(Hostel $hostel)
    {
        $this->hostel = $hostel;
    }

    public function render()
    {
        return view('hostels::livewire.admin.room-list', [
            'rooms' => $this->hostel->rooms()->paginate(),
        ])->layout('layouts.app');
    }
}
