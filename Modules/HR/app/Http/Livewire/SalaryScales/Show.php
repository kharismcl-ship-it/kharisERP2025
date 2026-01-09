<?php

namespace Modules\HR\Http\Livewire\SalaryScales;

use Livewire\Component;
use Modules\HR\Models\SalaryScale;

class Show extends Component
{
    public SalaryScale $scale;

    public function mount(SalaryScale $scale)
    {
        $this->scale = $scale;
    }

    public function render()
    {
        return view('hr::livewire.salary-scales.show')
            ->layout('hr::layouts.master');
    }
}
