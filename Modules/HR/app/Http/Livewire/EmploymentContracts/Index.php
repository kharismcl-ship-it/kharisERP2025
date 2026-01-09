<?php

namespace Modules\HR\Http\Livewire\EmploymentContracts;

use Livewire\Component;
use Modules\HR\Models\EmploymentContract;

class Index extends Component
{
    public function render()
    {
        $contracts = EmploymentContract::with(['employee', 'company'])
            ->latest()
            ->paginate(10);

        return view('hr::livewire.employment-contracts.index', compact('contracts'))
            ->layout('hr::layouts.master');
    }
}
