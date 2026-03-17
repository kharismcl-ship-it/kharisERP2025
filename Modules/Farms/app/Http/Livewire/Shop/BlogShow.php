<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Modules\Farms\Models\FarmShopBlogPost;
use Modules\Farms\Services\ShopSettingsService;

class BlogShow extends Component
{
    public FarmShopBlogPost $post;

    public function mount(string $slug): void
    {
        $settings  = app(ShopSettingsService::class)->forCurrentDomain();
        $companyId = $settings?->company_id;

        $this->post = FarmShopBlogPost::published()
            ->where('slug', $slug)
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->firstOrFail();
    }

    public function render()
    {
        return view('farms::livewire.shop.blog-show', ['post' => $this->post])
            ->layout('farms::layouts.public', ['title' => $this->post->title]);
    }
}
