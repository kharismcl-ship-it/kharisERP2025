<?php

namespace Modules\Hostels\Http\Livewire\Admin;

use Livewire\Component;
use Modules\Hostels\Models\HostelCharge;

class HostelChargeList extends Component
{
    public function render()
    {
        return view('hostels::livewire.admin.hostel-charge-list', [
            'hostelCharges' => HostelCharge::where('company_id', auth()->user()->currentCompanyId())->paginate(),
        ])->layout('layouts.app');
    }
}
