<?php

namespace Modules\Hostels\Http\Livewire;

use Livewire\Component;
use Modules\Hostels\Models\HostelWhatsAppGroup;
use Modules\Hostels\Models\WhatsAppGroupMessage;
use Livewire\WithPagination;

class WhatsAppGroupMessages extends Component
{
    use WithPagination;

    public HostelWhatsAppGroup $group;

    public function mount(HostelWhatsAppGroup $group)
    {
        $this->group = $group;
    }

    public function render()
    {
        return view('hostels::livewire.whatsapp-group-messages', [
            'messages' => WhatsAppGroupMessage::where('whatsapp_group_id', $this->group->id)->latest()->paginate(20),
        ]);
    }
}
