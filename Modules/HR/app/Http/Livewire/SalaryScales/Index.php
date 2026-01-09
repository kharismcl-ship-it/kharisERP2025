<?php

namespace Modules\HR\Http\Livewire\SalaryScales;

use Livewire\Component;
use Modules\HR\Models\SalaryScale;

class Index extends Component
{
    public function render()
    {
        $scales = SalaryScale::with(['company'])
            ->latest()
            ->paginate(10);

        return view('hr::livewire.salary-scales.index', compact('scales'))
            ->layout('hr::layouts.master');
    }
}
