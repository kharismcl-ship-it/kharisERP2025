<?php

namespace Modules\Hostels\Http\Livewire;

use Livewire\Component;
use Modules\Hostels\Models\HostelWhatsAppGroup;
use Livewire\WithPagination;

class HostelWhatsAppGroupList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('hostels::livewire.hostel-whatsapp-group-list', [
            'groups' => HostelWhatsAppGroup::paginate(10),
        ]);
    }
}
