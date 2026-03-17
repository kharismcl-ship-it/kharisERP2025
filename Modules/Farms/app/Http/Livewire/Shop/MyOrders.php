<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Farms\Models\FarmLoyaltyPoint;
use Modules\Farms\Models\FarmOrder;
use Modules\Farms\Services\ShopSettingsService;

class MyOrders extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth('shop_customer')->check()) {
            session(['farm_shop_intended' => url()->current()]);
            $this->redirect(route('farm-shop.login'));
        }
    }

    public function cancelOrder(int $orderId): void
    {
        $order = FarmOrder::with('items')
            ->where('id', $orderId)
            ->where('shop_customer_id', auth('shop_customer')->id())
            ->first();

        if (! $order) {
            session()->flash('error', 'Order not found.');
            return;
        }

        if ($order->status !== 'pending') {
            session()->flash('error', 'Only pending orders can be cancelled.');
            return;
        }

        $order->update(['status' => 'cancelled']);
        session()->flash('success', "Order {$order->ref} has been cancelled.");
    }

    public function reOrder(int $orderId): void
    {
        $order = FarmOrder::with('items')
            ->where('id', $orderId)
            ->where('shop_customer_id', auth('shop_customer')->id())
            ->first();

        if (! $order) {
            session()->flash('error', 'Order not found.');
            return;
        }

        $cart = session('farm_shop_cart', ['company_id' => null, 'items' => []]);
        $cart['company_id'] = $order->company_id;
        $added = 0;

        foreach ($order->items as $item) {
            if (! $item->farm_produce_inventory_id) {
                continue;
            }

            $inventory = \Modules\Farms\Models\FarmProduceInventory::find($item->farm_produce_inventory_id);
            if (! $inventory || ! $inventory->marketplace_listed || $inventory->current_stock <= 0) {
                continue;
            }

            $id = $inventory->id;
            if (isset($cart['items'][$id])) {
                $cart['items'][$id]['quantity'] += $item->quantity;
                $cart['items'][$id]['subtotal']   = round($cart['items'][$id]['quantity'] * $inventory->unit_price, 2);
            } else {
                $cart['items'][$id] = [
                    'inventory_id' => $id,
                    'product_name' => $inventory->product_name,
                    'unit'         => $inventory->unit,
                    'unit_price'   => (float) $inventory->unit_price,
                    'quantity'     => min($item->quantity, $inventory->current_stock),
                    'subtotal'     => round(min($item->quantity, $inventory->current_stock) * $inventory->unit_price, 2),
                    'farm_name'    => $inventory->farm?->name ?? '',
                    'company_id'   => $inventory->company_id,
                ];
            }
            $added++;
        }

        if ($added === 0) {
            session()->flash('error', 'None of the items from this order are currently available.');
            return;
        }

        session(['farm_shop_cart' => $cart]);
        $this->redirect(route('farm-shop.cart'));
    }

    public function render()
    {
        $customer = auth('shop_customer')->user();

        $orders = FarmOrder::with('items')
            ->where('shop_customer_id', $customer->id)
            ->latest('placed_at')
            ->paginate(10);

        $loyaltyBalance = 0;
        $loyaltyEnabled = false;
        if ($customer) {
            $companyId = FarmOrder::where('shop_customer_id', $customer->id)->value('company_id');
            if ($companyId) {
                $settings = app(ShopSettingsService::class)->get($companyId);
                $loyaltyEnabled = (bool) ($settings?->loyalty_enabled ?? false);
                if ($loyaltyEnabled) {
                    $loyaltyBalance = FarmLoyaltyPoint::getBalance($customer->id, $companyId);
                }
            }
        }

        return view('farms::livewire.shop.my-orders', compact('orders', 'loyaltyBalance', 'loyaltyEnabled'))
            ->layout('farms::layouts.public', ['title' => 'My Orders']);
    }
}
