<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Modules\Farms\Models\FarmOrder;
use Modules\Farms\Services\ShopSettingsService;

class OrderReceipt extends Component
{
    public FarmOrder $order;

    public function mount(FarmOrder $order): void
    {
        // Allow access to logged-in customer who owns the order OR anyone with ref+phone
        $customer = auth('shop_customer')->user();

        if ($customer) {
            abort_unless($order->shop_customer_id === $customer->id, 403);
        } else {
            // Public access: require ref + phone query params
            $ref   = request()->query('ref');
            $phone = request()->query('phone');
            abort_unless(
                $ref && $phone && $order->ref === $ref && $order->customer_phone === $phone,
                403
            );
        }

        $this->order = $order->load('items', 'company');
    }

    public function render()
    {
        $settings = null;
        try {
            $settings = app(ShopSettingsService::class)->get($this->order->company_id);
        } catch (\Throwable) {}

        return view('farms::livewire.shop.order-receipt', compact('settings'))
            ->layout('farms::layouts.receipt');
    }
}
