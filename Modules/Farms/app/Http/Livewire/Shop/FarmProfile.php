<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmProduceInventory;
use Modules\Farms\Services\ShopSettingsService;

class FarmProfile extends Component
{
    public Farm $farm;

    public function mount(string $slug): void
    {
        $settings  = app(ShopSettingsService::class)->forCurrentDomain();
        $companyId = $settings?->company_id;

        $this->farm = Farm::where('slug', $slug)
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->where('status', 'active')
            ->firstOrFail();
    }

    public function render()
    {
        $produce = FarmProduceInventory::where('farm_id', $this->farm->id)
            ->where('marketplace_listed', true)
            ->where('status', '!=', 'depleted')
            ->latest('harvest_date')
            ->take(6)
            ->get();

        return view('farms::livewire.shop.farm-profile', [
            'farm'    => $this->farm,
            'produce' => $produce,
        ])->layout('farms::layouts.public', ['title' => 'About ' . $this->farm->name]);
    }
}
