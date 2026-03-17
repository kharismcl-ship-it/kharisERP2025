<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Modules\Farms\Models\FarmAbandonedCart;
use Modules\Farms\Models\FarmCoupon;
use Modules\Farms\Models\FarmProduceInventory;

class Cart extends Component
{
    public array  $items      = [];
    public float  $cartTotal  = 0;
    public ?int   $companyId  = null;

    // Coupon
    public string  $couponInput     = '';
    public ?array  $appliedCoupon   = null; // ['code', 'discount_amount', 'label']
    public ?string $couponError     = null;

    public function mount(): void
    {
        $this->loadCart();
    }

    private function loadCart(): void
    {
        $cart            = session('farm_shop_cart', ['company_id' => null, 'items' => []]);
        $this->items     = $cart['items'] ?? [];
        $this->companyId = $cart['company_id'];
        $this->cartTotal = round(array_sum(array_column($this->items, 'subtotal')), 2);
        $this->appliedCoupon = $cart['coupon'] ?? null;
    }

    public function applyCoupon(): void
    {
        $this->couponError = null;
        $code = strtoupper(trim($this->couponInput));

        if (! $code) {
            $this->couponError = 'Please enter a coupon code.';
            return;
        }

        if (! $this->companyId) {
            $this->couponError = 'Add items to your cart first.';
            return;
        }

        $coupon = FarmCoupon::where('company_id', $this->companyId)
            ->where('code', $code)
            ->first();

        if (! $coupon) {
            $this->couponError = 'Coupon code not found.';
            return;
        }

        if (! $coupon->isValidFor($this->cartTotal)) {
            if ($coupon->min_order_amount && $this->cartTotal < (float) $coupon->min_order_amount) {
                $this->couponError = "Minimum order of GHS {$coupon->min_order_amount} required for this coupon.";
            } else {
                $this->couponError = 'This coupon is not valid or has expired.';
            }
            return;
        }

        $discountAmount = $coupon->calculateDiscount($this->cartTotal);
        $label = $coupon->type === 'percentage'
            ? "{$coupon->discount_value}% off"
            : "GHS {$coupon->discount_value} off";

        $cart = session('farm_shop_cart', ['company_id' => null, 'items' => []]);
        $cart['coupon'] = [
            'code'            => $code,
            'discount_amount' => $discountAmount,
            'label'           => $label,
        ];
        session(['farm_shop_cart' => $cart]);

        $this->couponInput   = '';
        $this->appliedCoupon = $cart['coupon'];
    }

    public function removeCoupon(): void
    {
        $cart = session('farm_shop_cart', ['company_id' => null, 'items' => []]);
        unset($cart['coupon']);
        session(['farm_shop_cart' => $cart]);
        $this->appliedCoupon = null;
        $this->couponError   = null;
    }

    public function updateQuantity(int|string $inventoryId, float $quantity): void
    {
        $id = (int) $inventoryId;
        if ($quantity <= 0) {
            $this->removeItem($id);
            return;
        }

        $product = FarmProduceInventory::with('priceTiers')->find($id);
        if (! $product) {
            return;
        }

        $maxQty   = (float) $product->current_stock;
        $quantity = min($quantity, $maxQty);

        // Apply tier pricing based on new quantity
        $tierPrice = $product->getApplicableTierPrice($quantity);
        $unitPrice = $tierPrice ?? (float) $product->unit_price;

        $cart = session('farm_shop_cart', ['company_id' => null, 'items' => []]);
        if (isset($cart['items'][$id])) {
            $cart['items'][$id]['quantity']   = $quantity;
            $cart['items'][$id]['unit_price']  = $unitPrice;
            $cart['items'][$id]['subtotal']    = round($quantity * $unitPrice, 2);

            // Find tier label
            $tierLabel = null;
            foreach ($product->priceTiers as $tier) {
                if ($quantity >= (float) $tier->min_quantity) {
                    $tierLabel = $tier->label;
                }
            }
            $cart['items'][$id]['tier_label'] = $tierLabel;

            session(['farm_shop_cart' => $cart]);
        }

        // Update abandoned cart tracking
        $customer = auth('shop_customer')->user();
        if ($customer) {
            FarmAbandonedCart::saveForCustomer($customer, $cart);
        }

        $this->loadCart();
    }

    public function removeItem(int|string $inventoryId): void
    {
        $id   = (int) $inventoryId;
        $cart = session('farm_shop_cart', ['company_id' => null, 'items' => []]);
        unset($cart['items'][$id]);

        if (empty($cart['items'])) {
            $cart['company_id'] = null;
            unset($cart['coupon']); // clear coupon when cart empties

            // Clear abandoned cart tracking
            $customer = auth('shop_customer')->user();
            if ($customer) {
                FarmAbandonedCart::clearForCustomer($customer->id);
            }
        } else {
            $customer = auth('shop_customer')->user();
            if ($customer) {
                FarmAbandonedCart::saveForCustomer($customer, $cart);
            }
        }

        session(['farm_shop_cart' => $cart]);
        $this->loadCart();
    }

    public function clearCart(): void
    {
        session()->forget('farm_shop_cart');
        $this->items         = [];
        $this->cartTotal     = 0;
        $this->companyId     = null;
        $this->appliedCoupon = null;

        $customer = auth('shop_customer')->user();
        if ($customer) {
            FarmAbandonedCart::clearForCustomer($customer->id);
        }
    }

    public function proceedToCheckout()
    {
        if (empty($this->items)) {
            session()->flash('error', 'Your cart is empty.');
            return;
        }
        return $this->redirect(route('farm-shop.checkout'));
    }

    public function render()
    {
        return view('farms::livewire.shop.cart')
            ->layout('farms::layouts.public', ['title' => 'Cart']);
    }
}
