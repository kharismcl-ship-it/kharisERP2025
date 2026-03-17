<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Modules\Farms\Models\FarmShopPage as ShopPageModel;
use Modules\Farms\Services\ShopSettingsService;

class FarmShopPage extends Component
{
    public ShopPageModel $page;

    public function mount(string $slug): void
    {
        $settings  = app(ShopSettingsService::class)->forCurrentDomain();
        $companyId = $settings?->company_id;

        $this->page = ShopPageModel::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->firstOrFail();
    }

    public function render()
    {
        return view('farms::livewire.shop.farm-shop-page', ['page' => $this->page])
            ->layout('farms::layouts.public', ['title' => $this->page->title]);
    }
}
