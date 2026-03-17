<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Modules\Farms\Models\FarmCustomerWishlist;

class MyWishlist extends Component
{
    public function mount(): void
    {
        if (! auth('shop_customer')->check()) {
            session(['farm_shop_intended' => url()->current()]);
            $this->redirect(route('farm-shop.login'));
        }
    }

    public function removeFromWishlist(int $wishlistId): void
    {
        FarmCustomerWishlist::where('id', $wishlistId)
            ->where('shop_customer_id', auth('shop_customer')->id())
            ->delete();

        session()->flash('success', 'Removed from wishlist.');
    }

    public function addToCart(int $inventoryId): void
    {
        $inventory = \Modules\Farms\Models\FarmProduceInventory::find($inventoryId);
        if (! $inventory || ! $inventory->marketplace_listed || $inventory->current_stock <= 0) {
            session()->flash('error', 'This product is no longer available.');
            return;
        }

        $cart = session('farm_shop_cart', ['company_id' => null, 'items' => []]);

        if ($cart['company_id'] && $cart['company_id'] !== $inventory->company_id && count($cart['items']) > 0) {
            session()->flash('error', 'Your cart contains items from a different company. Please clear your cart first.');
            return;
        }

        $cart['company_id'] = $inventory->company_id;
        $id = $inventory->id;
        $qty = max(1, (float) ($inventory->min_order_quantity ?? 1));

        if (isset($cart['items'][$id])) {
            $cart['items'][$id]['quantity'] += $qty;
            $cart['items'][$id]['subtotal']   = round($cart['items'][$id]['quantity'] * $inventory->unit_price, 2);
        } else {
            $cart['items'][$id] = [
                'inventory_id' => $id,
                'product_name' => $inventory->product_name,
                'unit'         => $inventory->unit,
                'unit_price'   => (float) $inventory->unit_price,
                'quantity'     => $qty,
                'subtotal'     => round($qty * $inventory->unit_price, 2),
                'farm_name'    => $inventory->farm?->name ?? '',
                'company_id'   => $inventory->company_id,
            ];
        }

        session(['farm_shop_cart' => $cart]);
        session()->flash('success', "'{$inventory->product_name}' added to cart.");
        $this->redirect(route('farm-shop.cart'));
    }

    public function render()
    {
        $wishlistItems = FarmCustomerWishlist::with(['product.farm'])
            ->where('shop_customer_id', auth('shop_customer')->id())
            ->latest()
            ->get();

        return view('farms::livewire.shop.my-wishlist', compact('wishlistItems'))
            ->layout('farms::layouts.public', ['title' => 'My Wishlist']);
    }
}
