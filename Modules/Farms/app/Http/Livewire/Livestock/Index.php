<?php

namespace Modules\Farms\Http\Livewire\Livestock;

use Livewire\Component;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\LivestockBatch;

class Index extends Component
{
    public Farm $farm;

    public string $statusFilter = 'active';

    public function mount(Farm $farm): void
    {
        $this->farm = $farm;
    }

    public function getBatchesProperty()
    {
        return LivestockBatch::where('farm_id', $this->farm->id)
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderByDesc('acquisition_date')
            ->paginate(20);
    }

    public function render()
    {
        return view('farms::livewire.livestock.index', [
            'batches' => $this->batches,
        ])->layout('farms::layouts.app');
    }
}
