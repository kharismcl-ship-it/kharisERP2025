<?php

namespace Modules\HR\Http\Livewire\PerformanceCycles;

use Livewire\Component;
use Modules\HR\Models\PerformanceCycle;

class Index extends Component
{
    public function render()
    {
        $cycles = PerformanceCycle::with(['company'])
            ->latest()
            ->paginate(10);

        return view('hr::livewire.performance-cycles.index', compact('cycles'))
            ->layout('hr::layouts.master');
    }
}
