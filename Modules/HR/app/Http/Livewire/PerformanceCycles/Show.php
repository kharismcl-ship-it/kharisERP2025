<?php

namespace Modules\HR\Http\Livewire\PerformanceCycles;

use Livewire\Component;
use Modules\HR\Models\PerformanceCycle;

class Show extends Component
{
    public PerformanceCycle $cycle;

    public function mount(PerformanceCycle $cycle)
    {
        $this->cycle = $cycle;
    }

    public function render()
    {
        return view('hr::livewire.performance-cycles.show')
            ->layout('hr::layouts.master');
    }
}
