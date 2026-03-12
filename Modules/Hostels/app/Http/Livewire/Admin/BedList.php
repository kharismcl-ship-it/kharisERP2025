<?php

namespace Modules\Hostels\Http\Livewire\Admin;

use Livewire\Component;
use Modules\Hostels\Models\Room;

class BedList extends Component
{
    public Room $room;

    public function mount(Room $room)
    {
        $this->room = $room;
    }

    public function render()
    {
        return view('hostels::livewire.admin.bed-list', [
            'beds' => $this->room->beds()->paginate(),
        ])->layout('layouts.app');
    }
}
