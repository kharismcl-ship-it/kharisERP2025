<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Modules\Farms\Models\FarmAbandonedCart;
use Modules\Farms\Models\FarmBundle;
use Modules\Farms\Models\FarmProduceInventory;

class BundleShow extends Component
{
    public FarmBundle $bundle;

    public function mount(FarmBundle $bundle): void
    {
        abort_unless($bundle->is_active, 404);
        $this->bundle = $bundle->load('bundleItems.product.priceTiers');
    }

    public function addToCart(): void
    {
        $cart = session('farm_shop_cart', ['company_id' => null, 'items' => []]);

        $companyId = $this->bundle->company_id;
        if ($cart['company_id'] && $cart['company_id'] !== $companyId && count($cart['items']) > 0) {
            session()->flash('error', 'Your cart contains items from a different company. Please clear your cart first.');
            return;
        }

        $discount = (float) $this->bundle->discount_percentage / 100;

        foreach ($this->bundle->bundleItems as $bundleItem) {
            $product = $bundleItem->product;

            if (! $product || ! $product->marketplace_listed) {
                continue;
            }

            if ($product->current_stock < (float) $bundleItem->quantity) {
                session()->flash('error', "Sorry, '{$product->product_name}' doesn't have enough stock for this bundle.");
                return;
            }
        }

        $cart['company_id'] = $companyId;

        foreach ($this->bundle->bundleItems as $bundleItem) {
            $product  = $bundleItem->product;
            if (! $product) {
                continue;
            }

            $basePrice    = $product->getEffectiveBasePrice();
            $bundlePrice  = round($basePrice * (1 - $discount), 2);
            $qty          = (float) $bundleItem->quantity;
            $id           = $product->id;

            if (isset($cart['items'][$id])) {
                $newQty = $cart['items'][$id]['quantity'] + $qty;
                $cart['items'][$id]['quantity']   = $newQty;
                $cart['items'][$id]['unit_price']  = $bundlePrice;
                $cart['items'][$id]['subtotal']    = round($newQty * $bundlePrice, 2);
                $cart['items'][$id]['bundle_name'] = $this->bundle->name;
            } else {
                $cart['items'][$id] = [
                    'inventory_id' => $id,
                    'product_name' => $product->product_name,
                    'unit'         => $product->unit,
                    'unit_price'   => $bundlePrice,
                    'quantity'     => $qty,
                    'subtotal'     => round($qty * $bundlePrice, 2),
                    'farm_name'    => $product->farm?->name ?? '',
                    'company_id'   => $companyId,
                    'bundle_name'  => $this->bundle->name,
                    'tier_label'   => null,
                ];
            }
        }

        session(['farm_shop_cart' => $cart]);

        $customer = auth('shop_customer')->user();
        if ($customer) {
            FarmAbandonedCart::saveForCustomer($customer, $cart);
        }

        session()->flash('success', "Bundle '{$this->bundle->name}' added to cart!");
        $this->redirect(route('farm-shop.cart'));
    }

    public function render()
    {
        return view('farms::livewire.shop.bundle-show')
            ->layout('farms::layouts.public', ['title' => $this->bundle->name]);
    }
}
