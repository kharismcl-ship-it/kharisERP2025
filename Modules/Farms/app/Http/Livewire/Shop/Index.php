<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmBundle;
use Modules\Farms\Models\FarmProduceInventory;
use Modules\Farms\Models\FarmShopBanner;
use Modules\Farms\Services\ShopSettingsService;

class Index extends Component
{
    use WithPagination;

    public string $search   = '';
    public string $farmFilter = '';
    public string $typeFilter = '';

    protected $queryString = ['search', 'farmFilter', 'typeFilter'];

    public function updatedSearch(): void    { $this->resetPage(); }
    public function updatedFarmFilter(): void  { $this->resetPage(); }
    public function updatedTypeFilter(): void  { $this->resetPage(); }

    public function render()
    {
        $farms = Farm::where('status', 'active')->orderBy('name')->get();

        $products = FarmProduceInventory::with('farm')
            ->where('marketplace_listed', true)
            ->whereIn('status', ['in_stock', 'low_stock'])
            ->where('current_stock', '>', 0)
            ->whereNotNull('unit_price')
            ->when($this->search, fn ($q) => $q->where('product_name', 'like', "%{$this->search}%"))
            ->when($this->farmFilter, fn ($q) => $q->where('farm_id', $this->farmFilter))
            ->orderBy('product_name')
            ->paginate(12);

        $bundles = FarmBundle::with('bundleItems.product')
            ->active()
            ->orderBy('name')
            ->get();

        $settings  = app(ShopSettingsService::class)->forCurrentDomain();
        $companyId = $settings?->company_id;

        $banners = FarmShopBanner::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->visible()
            ->get();

        return view('farms::livewire.shop.index', compact('products', 'farms', 'bundles', 'banners'))
            ->layout('farms::layouts.public', ['title' => 'Shop Fresh Produce — Alpha Farms']);
    }
}
