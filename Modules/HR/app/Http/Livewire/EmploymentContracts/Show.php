<?php

namespace Modules\HR\Http\Livewire\EmploymentContracts;

use Livewire\Component;
use Modules\HR\Models\EmploymentContract;

class Show extends Component
{
    public EmploymentContract $contract;

    public function mount(EmploymentContract $contract)
    {
        $this->contract = $contract;
    }

    public function render()
    {
        return view('hr::livewire.employment-contracts.show')
            ->layout('hr::layouts.master');
    }
}
