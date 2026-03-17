<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Modules\Farms\Models\FarmSubscription;

class MySubscriptions extends Component
{
    public function mount(): void
    {
        if (! auth('shop_customer')->check()) {
            session(['farm_shop_intended' => url()->current()]);
            $this->redirect(route('farm-shop.login'));
        }
    }

    public function pauseSubscription(int $id): void
    {
        $sub = FarmSubscription::where('id', $id)
            ->where('shop_customer_id', auth('shop_customer')->id())
            ->where('status', 'active')
            ->first();

        if ($sub) {
            $sub->update(['status' => 'paused', 'paused_at' => now()]);
            session()->flash('success', 'Subscription paused.');
        }
    }

    public function resumeSubscription(int $id): void
    {
        $sub = FarmSubscription::where('id', $id)
            ->where('shop_customer_id', auth('shop_customer')->id())
            ->where('status', 'paused')
            ->first();

        if ($sub) {
            $sub->update(['status' => 'active', 'paused_at' => null]);
            session()->flash('success', 'Subscription resumed.');
        }
    }

    public function cancelSubscription(int $id): void
    {
        $sub = FarmSubscription::where('id', $id)
            ->where('shop_customer_id', auth('shop_customer')->id())
            ->whereIn('status', ['active', 'paused'])
            ->first();

        if ($sub) {
            $sub->update(['status' => 'cancelled', 'cancelled_at' => now()]);
            session()->flash('success', 'Subscription cancelled.');
        }
    }

    public function render()
    {
        $subscriptions = FarmSubscription::with('company')
            ->where('shop_customer_id', auth('shop_customer')->id())
            ->orderByDesc('created_at')
            ->get();

        return view('farms::livewire.shop.my-subscriptions', compact('subscriptions'))
            ->layout('farms::layouts.public', ['title' => 'My Subscriptions']);
    }
}
