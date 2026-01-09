<?php

namespace Modules\Hostels\Http\Livewire;

use Livewire\Component;
use Modules\Hostels\Models\HostelCharge;

class HostelChargeList extends Component
{
    public function render()
    {
        return view('hostels::livewire.hostel-charge-list', [
            'hostelCharges' => HostelCharge::where('company_id', auth()->user()->currentCompanyId())->paginate(),
        ])->layout('layouts.app');
    }
}
