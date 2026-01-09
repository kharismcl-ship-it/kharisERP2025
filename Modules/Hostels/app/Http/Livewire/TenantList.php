<?php

namespace Modules\Hostels\Http\Livewire;

use Livewire\Component;
use Modules\Hostels\Models\Tenant;

class TenantList extends Component
{
    public function render()
    {
        return view('hostels::livewire.tenant-list', [
            'tenants' => Tenant::where('company_id', auth()->user()->currentCompanyId())->paginate(),
        ])->layout('layouts.app');
    }
}
