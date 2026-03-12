<?php

namespace Modules\Farms\Http\Livewire\Crops;

use Livewire\Component;
use Modules\Farms\Models\CropCycle;
use Modules\Farms\Models\Farm;

class Index extends Component
{
    public Farm $farm;

    public string $statusFilter = '';

    public function mount(Farm $farm): void
    {
        $this->farm = $farm;
    }

    public function getCyclesProperty()
    {
        return CropCycle::where('farm_id', $this->farm->id)
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderByDesc('planting_date')
            ->paginate(20);
    }

    public function render()
    {
        return view('farms::livewire.crops.index', [
            'cycles' => $this->cycles,
        ])->layout('farms::layouts.app');
    }
}
