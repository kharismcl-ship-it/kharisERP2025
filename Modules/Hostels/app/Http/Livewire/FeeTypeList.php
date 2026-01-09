<?php

namespace Modules\Hostels\Http\Livewire;

use Livewire\Component;
use Modules\Hostels\Models\FeeType;

class FeeTypeList extends Component
{
    public function render()
    {
        return view('hostels::livewire.fee-type-list', [
            'feeTypes' => FeeType::where('company_id', auth()->user()->currentCompanyId())->paginate(),
        ])->layout('layouts.app');
    }
}
