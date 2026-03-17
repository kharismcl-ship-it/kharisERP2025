<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Farms\Models\FarmShopBlogPost;
use Modules\Farms\Services\ShopSettingsService;

class BlogIndex extends Component
{
    use WithPagination;

    public string $category = '';

    public string $search = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingCategory(): void { $this->resetPage(); }

    public function render()
    {
        $settings  = app(ShopSettingsService::class)->forCurrentDomain();
        $companyId = $settings?->company_id;

        $posts = FarmShopBlogPost::published()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($this->category, fn ($q) => $q->where('category', $this->category))
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $this->search . '%');
            }))
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('farms::livewire.shop.blog-index', compact('posts', 'settings'))
            ->layout('farms::layouts.public', ['title' => 'Blog & Recipes']);
    }
}
