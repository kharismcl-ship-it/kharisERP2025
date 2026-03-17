<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Modules\Farms\Models\FarmCustomerWishlist;
use Modules\Farms\Models\FarmProductReview;
use Modules\Farms\Models\FarmProduceInventory;
use Modules\Farms\Models\FarmRestockNotification;

class Show extends Component
{
    public FarmProduceInventory $product;
    public float $quantity = 1;

    // Tier / flash sale pricing
    public float   $effectivePrice   = 0;
    public ?string $activeTierLabel  = null;
    public bool    $isOnSale         = false;
    public float   $originalUnitPrice = 0;
    public ?string $saleEndsAt       = null; // ISO string for countdown timer

    // Review form
    public int    $rating      = 5;
    public string $reviewText  = '';
    public bool   $showReviewForm = false;
    public bool   $alreadyReviewed = false;

    // Wishlist
    public bool $inWishlist = false;

    // B2B wholesale
    public bool  $isB2b            = false;
    public float $b2bDiscountPct   = 0.0;
    public float $b2bWholesalePrice = 0.0; // effective price after B2B discount

    // Restock subscription
    public string $subscribeEmail   = '';
    public bool   $subscribed       = false;
    public bool   $alreadySubscribed = false;

    public function mount(FarmProduceInventory $product): void
    {
        // Allow viewing out-of-stock products (for "Notify Me" feature)
        abort_unless($product->marketplace_listed && $product->unit_price, 404);

        $this->product         = $product->load('farm', 'priceTiers');
        $this->originalUnitPrice = (float) $product->unit_price;
        $this->isOnSale          = $product->isOnSale();
        $this->saleEndsAt        = $product->sale_ends_at?->toIso8601String();
        $this->effectivePrice    = $product->getEffectiveBasePrice();

        // Set default quantity
        $this->quantity = max(1, (float) ($product->min_order_quantity ?? 1));
        $this->recalculateTierPrice();

        $customer = auth('shop_customer')->user();
        if ($customer) {
            $this->alreadyReviewed = FarmProductReview::where('farm_produce_inventory_id', $product->id)
                ->where('shop_customer_id', $customer->id)
                ->exists();

            $this->inWishlist = FarmCustomerWishlist::where('shop_customer_id', $customer->id)
                ->where('farm_produce_inventory_id', $product->id)
                ->exists();

            $this->alreadySubscribed = FarmRestockNotification::where('farm_produce_inventory_id', $product->id)
                ->where('shop_customer_id', $customer->id)
                ->pending()
                ->exists();

            $this->subscribeEmail = $customer->email ?? '';

            // B2B wholesale pricing
            $b2bPct = $customer->getB2bDiscountPercent();
            if ($b2bPct > 0) {
                $this->isB2b           = true;
                $this->b2bDiscountPct  = $b2bPct;
                $this->b2bWholesalePrice = round($this->effectivePrice * (1 - $b2bPct / 100), 2);
                // Override effective price so cart gets wholesale price
                $this->effectivePrice = $this->b2bWholesalePrice;
            }
        }
    }

    private function recalculateTierPrice(): void
    {
        // When product is on sale, use sale price — tier pricing doesn't stack with sale
        if ($this->product->isOnSale()) {
            $this->effectivePrice  = $this->product->getEffectiveBasePrice();
            $this->activeTierLabel = null;
            return;
        }

        $tiers = $this->product->priceTiers;
        if ($tiers->isEmpty()) {
            $this->effectivePrice  = (float) $this->product->unit_price;
            $this->activeTierLabel = null;
            return;
        }

        $applicable      = null;
        $applicableLabel = null;
        foreach ($tiers as $tier) {
            if ($this->quantity >= (float) $tier->min_quantity) {
                $applicable      = (float) $tier->price_per_unit;
                $applicableLabel = $tier->label;
            }
        }

        $basePrice = $applicable ?? (float) $this->product->unit_price;

        // Apply B2B discount on top of tier/base price
        if ($this->isB2b && $this->b2bDiscountPct > 0) {
            $this->effectivePrice = round($basePrice * (1 - $this->b2bDiscountPct / 100), 2);
            $this->b2bWholesalePrice = $this->effectivePrice;
        } else {
            $this->effectivePrice = $basePrice;
        }

        $this->activeTierLabel = $applicableLabel;
    }

    public function updatedQuantity(): void
    {
        $this->recalculateTierPrice();
    }

    public function toggleWishlist(): void
    {
        $customer = auth('shop_customer')->user();
        if (! $customer) {
            session(['farm_shop_intended' => url()->current()]);
            $this->redirect(route('farm-shop.login'));
            return;
        }

        if ($this->inWishlist) {
            FarmCustomerWishlist::where('shop_customer_id', $customer->id)
                ->where('farm_produce_inventory_id', $this->product->id)
                ->delete();
            $this->inWishlist = false;
        } else {
            FarmCustomerWishlist::firstOrCreate([
                'shop_customer_id'          => $customer->id,
                'farm_produce_inventory_id' => $this->product->id,
            ]);
            $this->inWishlist = true;
        }
    }

    public function subscribeToRestock(): void
    {
        $customer = auth('shop_customer')->user();

        $email = $customer ? ($customer->email ?? '') : trim($this->subscribeEmail);

        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            session()->flash('restock_error', 'Please enter a valid email address.');
            return;
        }

        FarmRestockNotification::firstOrCreate(
            [
                'farm_produce_inventory_id' => $this->product->id,
                'shop_customer_id'          => $customer?->id,
                'email'                     => $customer ? $customer->email : $email,
            ],
            [
                'company_id' => $this->product->company_id,
                'phone'      => $customer?->phone,
            ]
        );

        $this->alreadySubscribed = true;
        $this->subscribed        = true;
        $this->subscribeEmail    = '';
    }

    public function addToCart(): void
    {
        $outOfStock = $this->product->current_stock <= 0 || $this->product->status === 'depleted';
        if ($outOfStock) {
            session()->flash('error', 'This product is currently out of stock.');
            return;
        }

        $min = (float) ($this->product->min_order_quantity ?? 0);
        if ($min > 0 && $this->quantity < $min) {
            $this->dispatch('notify', type: 'error', message: "Minimum order is {$min} {$this->product->unit}.");
            return;
        }

        if ($this->quantity <= 0 || $this->quantity > $this->product->current_stock) {
            $this->dispatch('notify', type: 'error', message: 'Invalid quantity.');
            return;
        }

        $cart = session('farm_shop_cart', ['company_id' => null, 'items' => []]);

        $productCompanyId = $this->product->company_id;
        if ($cart['company_id'] && $cart['company_id'] !== $productCompanyId && count($cart['items']) > 0) {
            session()->flash('error', 'Your cart contains items from a different company. Please clear your cart first.');
            return;
        }

        $cart['company_id'] = $productCompanyId;
        $id = $this->product->id;

        // Apply tier price if applicable
        $unitPrice = $this->effectivePrice;

        if (isset($cart['items'][$id])) {
            $newQty = $cart['items'][$id]['quantity'] + $this->quantity;
            // Recalculate tier for combined quantity
            $this->quantity = $newQty;
            $this->recalculateTierPrice();
            $unitPrice = $this->effectivePrice;

            $cart['items'][$id]['quantity']   = $newQty;
            $cart['items'][$id]['unit_price']  = $unitPrice;
            $cart['items'][$id]['subtotal']    = round($newQty * $unitPrice, 2);
            if ($this->activeTierLabel) {
                $cart['items'][$id]['tier_label'] = $this->activeTierLabel;
            }
        } else {
            $cart['items'][$id] = [
                'inventory_id' => $id,
                'product_name' => $this->product->product_name,
                'unit'         => $this->product->unit,
                'unit_price'   => $unitPrice,
                'quantity'     => $this->quantity,
                'subtotal'     => round($this->quantity * $unitPrice, 2),
                'farm_name'    => $this->product->farm?->name ?? '',
                'company_id'   => $productCompanyId,
                'tier_label'   => $this->activeTierLabel,
            ];
        }

        session(['farm_shop_cart' => $cart]);

        // Save to abandoned carts tracking for logged-in customers
        $customer = auth('shop_customer')->user();
        if ($customer) {
            \Modules\Farms\Models\FarmAbandonedCart::saveForCustomer($customer, $cart);
        }

        session()->flash('success', "'{$this->product->product_name}' added to cart.");
        $this->redirect(route('farm-shop.cart'));
    }

    public function submitReview(): void
    {
        $customer = auth('shop_customer')->user();
        if (! $customer) {
            session(['farm_shop_intended' => url()->current()]);
            $this->redirect(route('farm-shop.login'));
            return;
        }

        if ($this->alreadyReviewed) {
            return;
        }

        $this->validate([
            'rating'     => ['required', 'integer', 'between:1,5'],
            'reviewText' => ['nullable', 'string', 'max:1000'],
        ]);

        FarmProductReview::create([
            'farm_produce_inventory_id' => $this->product->id,
            'shop_customer_id'          => $customer->id,
            'rating'                    => $this->rating,
            'review_text'               => $this->reviewText ?: null,
            'reviewer_name'             => $customer->name,
            'is_approved'               => true,
        ]);

        $this->alreadyReviewed = true;
        $this->showReviewForm  = false;
        $this->reviewText      = '';
        session()->flash('review_success', 'Thank you for your review!');
    }

    public function render()
    {
        $reviews = FarmProductReview::approved()
            ->where('farm_produce_inventory_id', $this->product->id)
            ->latest()
            ->get();

        $avgRating   = $reviews->isNotEmpty() ? round($reviews->avg('rating'), 1) : null;
        $reviewCount = $reviews->count();

        return view('farms::livewire.shop.show', compact('reviews', 'avgRating', 'reviewCount'))
            ->layout('farms::layouts.public', ['title' => $this->product->product_name]);
    }
}
