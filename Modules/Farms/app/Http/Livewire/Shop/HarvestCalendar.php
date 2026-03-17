<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Modules\Farms\Models\FarmProduceInventory;

class HarvestCalendar extends Component
{
    public ?int $companyId = null;

    public function mount(): void
    {
        // Optionally accept ?company= query param for multi-company shops
        $this->companyId = request()->query('company') ? (int) request()->query('company') : null;
    }

    public function render()
    {
        $upcomingHarvests = FarmProduceInventory::with('farm')
            ->when($this->companyId, fn ($q) => $q->where('company_id', $this->companyId))
            ->where('marketplace_listed', true)
            ->whereNotNull('harvest_date')
            ->where('harvest_date', '>=', now()->startOfMonth())
            ->orderBy('harvest_date')
            ->get()
            ->groupBy(fn ($p) => $p->harvest_date->format('Y-m'));

        return view('farms::livewire.shop.harvest-calendar', compact('upcomingHarvests'))
            ->layout('farms::layouts.public', ['title' => 'Harvest Calendar']);
    }
}
