<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Modules\Farms\Events\FarmOrderPlaced;
use Modules\Farms\Models\FarmAbandonedCart;
use Modules\Farms\Models\FarmCoupon;
use Modules\Farms\Models\FarmLoyaltyPoint;
use Modules\Farms\Models\FarmB2bAccount;
use Modules\Farms\Models\FarmSavedAddress;
use Modules\Farms\Models\FarmOrder;
use Modules\Farms\Models\FarmOrderItem;
use Modules\Farms\Models\FarmProduceInventory;
use Modules\Farms\Services\ShopSettingsService;

class Checkout extends Component
{
    public int    $step          = 1; // 1=details, 2=review
    public array  $cartItems     = [];
    public float  $cartTotal     = 0;
    public ?int   $companyId     = null;
    public float  $deliveryFee   = 20.00;
    public ?float $freeDeliveryAbove = null;

    // Available delivery slots (date => label)
    public array   $availableDeliverySlots = [];
    public ?string $cutoffTime = null;

    // Applied coupon from cart session
    public ?string $appliedCouponCode  = null;
    public float   $discountAmount     = 0.0;

    // Saved addresses (loaded for logged-in customers)
    public array $savedAddresses = [];

    // Loyalty points
    public bool  $loyaltyEnabled    = false;
    public int   $loyaltyBalance    = 0;
    public bool  $redeemLoyalty     = false;
    public int   $pointsToRedeem    = 0;
    public float $loyaltyDiscount   = 0.0;
    public float $loyaltyPointsPerGhs = 1.0;  // points earned per GHS
    public float $loyaltyValuePerPoint = 0.01; // GHS per point

    // B2B
    public bool  $isB2b           = false;
    public float $b2bDiscountPct  = 0.0;
    public float $b2bDiscountAmt  = 0.0;
    public string $b2bPaymentTerms = 'prepay';
    public string $poNumber        = '';

    // Step 1 fields
    public string $customerName          = '';
    public string $customerPhone         = '';
    public string $customerEmail         = '';
    public string $deliveryType          = 'pickup'; // pickup | delivery
    public string $deliveryAddress       = '';
    public string $deliveryLandmark      = '';
    public string $preferredDeliveryDate = '';
    public string $notes                 = '';

    protected function rules(): array
    {
        $deliveryDateRequired = $this->deliveryType === 'delivery' && count($this->availableDeliverySlots) > 0;

        return [
            'customerName'          => ['required', 'string', 'min:2', 'max:100'],
            'customerPhone'         => ['required', 'string', 'min:9', 'max:20'],
            'customerEmail'         => ['nullable', 'email', 'max:100'],
            'deliveryType'          => ['required', 'in:pickup,delivery'],
            'deliveryAddress'       => ['required_if:deliveryType,delivery', 'nullable', 'string', 'max:500'],
            'deliveryLandmark'      => ['nullable', 'string', 'max:255'],
            'preferredDeliveryDate' => $deliveryDateRequired ? ['required', 'date', 'after:today'] : ['nullable'],
            'notes'                 => ['nullable', 'string', 'max:500'],
            'poNumber'              => ['nullable', 'string', 'max:100'],
        ];
    }

    public function mount(): void
    {
        $cart = session('farm_shop_cart', ['company_id' => null, 'items' => []]);

        if (empty($cart['items'])) {
            session()->flash('error', 'Your cart is empty.');
            $this->redirect(route('farm-shop.cart'));
            return;
        }

        $this->cartItems = $cart['items'];
        $this->companyId = $cart['company_id'];
        $this->cartTotal = round(array_sum(array_column($this->cartItems, 'subtotal')), 2);

        // Load delivery fee + slots from shop settings
        if ($this->companyId) {
            $settings = app(ShopSettingsService::class)->get($this->companyId);
            $this->deliveryFee       = (float) ($settings->delivery_fee ?? 20.00);
            $this->freeDeliveryAbove = $settings->free_delivery_above ? (float) $settings->free_delivery_above : null;
            $this->cutoffTime        = $settings->order_cutoff_time;

            $deliveryDays = $settings->delivery_days ?? [];
            if (! empty($deliveryDays)) {
                $this->availableDeliverySlots = $this->generateDeliverySlots($deliveryDays);
            }
        }

        // Read coupon from cart session
        $coupon = $cart['coupon'] ?? null;
        if ($coupon) {
            $this->appliedCouponCode = $coupon['code'];
            $this->discountAmount    = (float) ($coupon['discount_amount'] ?? 0);
        }

        // Load loyalty settings
        if ($this->companyId) {
            $settings = app(ShopSettingsService::class)->get($this->companyId);
            $this->loyaltyEnabled      = (bool) ($settings->loyalty_enabled ?? false);
            $this->loyaltyPointsPerGhs = (float) ($settings->loyalty_points_per_ghs ?? 1.0);
            $this->loyaltyValuePerPoint = (float) ($settings->loyalty_points_value_ghs ?? 0.01);
        }

        // Pre-fill if customer is logged in
        $customer = auth('shop_customer')->user();
        if ($customer) {
            $this->customerName     = $customer->name;
            $this->customerPhone    = $customer->phone ?? '';
            $this->customerEmail    = $customer->email;
            $this->deliveryAddress  = $customer->default_address ?? '';
            $this->deliveryLandmark = $customer->default_landmark ?? '';

            if ($this->loyaltyEnabled && $this->companyId) {
                $this->loyaltyBalance = FarmLoyaltyPoint::getBalance($customer->id, $this->companyId);
            }

            $this->savedAddresses = FarmSavedAddress::where('shop_customer_id', $customer->id)
                ->orderByDesc('is_default')->orderBy('id')
                ->get(['id', 'label', 'address', 'landmark', 'is_default'])
                ->toArray();

            // B2B wholesale pricing
            if ($customer->is_b2b && $customer->b2b_account_id) {
                $b2bAccount = FarmB2bAccount::find($customer->b2b_account_id);
                if ($b2bAccount && $b2bAccount->isApproved()) {
                    $this->isB2b           = true;
                    $this->b2bDiscountPct  = (float) $b2bAccount->discount_percent;
                    $this->b2bPaymentTerms = $b2bAccount->payment_terms;
                    $this->b2bDiscountAmt  = round($this->cartTotal * $this->b2bDiscountPct / 100, 2);
                }
            }
        }
    }

    public function selectSavedAddress(int $id): void
    {
        $addr = collect($this->savedAddresses)->firstWhere('id', $id);
        if ($addr) {
            $this->deliveryAddress  = $addr['address'];
            $this->deliveryLandmark = $addr['landmark'] ?? '';
            $this->deliveryType     = 'delivery';
        }
    }

    protected function generateDeliverySlots(array $deliveryDays): array
    {
        $slots = [];
        for ($i = 1; $i <= 14; $i++) {
            $date    = now()->addDays($i);
            $dayName = $date->format('l');
            if (in_array($dayName, $deliveryDays)) {
                $slots[$date->format('Y-m-d')] = $date->format('D, M j');
            }
        }
        return $slots;
    }

    public function getEffectiveDeliveryFee(): float
    {
        if ($this->deliveryType !== 'delivery') {
            return 0.00;
        }
        if ($this->freeDeliveryAbove !== null && $this->cartTotal >= $this->freeDeliveryAbove) {
            return 0.00;
        }
        return $this->deliveryFee;
    }

    public function getOrderTotal(): float
    {
        return round(max(0, $this->cartTotal + $this->getEffectiveDeliveryFee() - $this->discountAmount - $this->loyaltyDiscount - $this->b2bDiscountAmt), 2);
    }

    public function updatedRedeemLoyalty(): void
    {
        if ($this->redeemLoyalty && $this->loyaltyBalance > 0) {
            // Calculate max points we can redeem (can't discount more than order total)
            $maxDiscount      = $this->cartTotal + $this->getEffectiveDeliveryFee() - $this->discountAmount;
            $maxPoints        = (int) floor($maxDiscount / $this->loyaltyValuePerPoint);
            $this->pointsToRedeem  = min($this->loyaltyBalance, $maxPoints);
            $this->loyaltyDiscount = round($this->pointsToRedeem * $this->loyaltyValuePerPoint, 2);
        } else {
            $this->pointsToRedeem  = 0;
            $this->loyaltyDiscount = 0.0;
        }
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate();
            $this->step = 2;
        }
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function placeOrder()
    {
        $this->validate();

        if (empty($this->cartItems) || ! $this->companyId) {
            session()->flash('error', 'Cart is empty or invalid.');
            return $this->redirect(route('farm-shop.cart'));
        }

        // Verify stock is still available
        foreach ($this->cartItems as $item) {
            $inventory = FarmProduceInventory::find($item['inventory_id']);
            if (! $inventory || $inventory->current_stock < $item['quantity']) {
                session()->flash('error', "Sorry, '{$item['product_name']}' is no longer available in the requested quantity.");
                return $this->redirect(route('farm-shop.cart'));
            }
        }

        $subtotal        = (float) $this->cartTotal;
        $deliveryFee     = $this->getEffectiveDeliveryFee();
        $discount        = $this->discountAmount;
        $loyaltyDiscount = $this->loyaltyDiscount;
        $b2bDiscount     = $this->b2bDiscountAmt;
        $total           = max(0, round($subtotal + $deliveryFee - $discount - $loyaltyDiscount - $b2bDiscount, 2));

        // Re-verify loyalty balance hasn't changed since page load
        $customer = auth('shop_customer')->user();
        if ($this->redeemLoyalty && $this->pointsToRedeem > 0 && $customer) {
            $currentBalance = FarmLoyaltyPoint::getBalance($customer->id, $this->companyId);
            if ($currentBalance < $this->pointsToRedeem) {
                $this->redeemLoyalty   = false;
                $this->loyaltyDiscount = 0.0;
                $this->pointsToRedeem  = 0;
                $loyaltyDiscount       = 0.0;
                $total                 = max(0, round($subtotal + $deliveryFee - $discount, 2));
                session()->flash('error', 'Your loyalty points balance changed. Please review your order total.');
                return $this->redirect(route('farm-shop.checkout'));
            }
        }

        // Create order
        $order = FarmOrder::create([
            'company_id'               => $this->companyId,
            'shop_customer_id'         => auth('shop_customer')->id(),
            'customer_name'            => $this->customerName,
            'customer_phone'           => $this->customerPhone,
            'customer_email'           => $this->customerEmail ?: null,
            'delivery_type'            => $this->deliveryType,
            'delivery_address'         => $this->deliveryType === 'delivery' ? $this->deliveryAddress : null,
            'delivery_landmark'        => $this->deliveryType === 'delivery' ? ($this->deliveryLandmark ?: null) : null,
            'preferred_delivery_date'  => $this->deliveryType === 'delivery' ? ($this->preferredDeliveryDate ?: null) : null,
            'coupon_code'              => $this->appliedCouponCode,
            'discount_amount'          => $discount,
            'loyalty_points_redeemed'  => $this->pointsToRedeem,
            'loyalty_discount'         => $loyaltyDiscount,
            'subtotal'                 => $subtotal,
            'delivery_fee'             => $deliveryFee,
            'total'                    => $total,
            'notes'                    => $this->notes ?: null,
            'status'                   => 'pending',
            'payment_status'           => 'pending',
            // B2B fields
            'is_b2b'                   => $this->isB2b,
            'b2b_account_id'           => $this->isB2b ? (auth('shop_customer')->user()?->b2b_account_id) : null,
            'po_number'                => $this->poNumber ?: null,
            'payment_terms'            => $this->isB2b ? $this->b2bPaymentTerms : null,
            'b2b_discount_amount'      => $b2bDiscount,
        ]);

        // Create order items and deduct stock
        foreach ($this->cartItems as $item) {
            FarmOrderItem::create([
                'farm_order_id'             => $order->id,
                'farm_produce_inventory_id' => $item['inventory_id'],
                'product_name'              => $item['product_name'],
                'unit'                      => $item['unit'],
                'quantity'                  => $item['quantity'],
                'unit_price'                => $item['unit_price'],
                'subtotal'                  => $item['subtotal'],
            ]);

            FarmProduceInventory::where('id', $item['inventory_id'])
                ->decrement('current_stock', $item['quantity']);

            $inventory = FarmProduceInventory::find($item['inventory_id']);
            if ($inventory && $inventory->current_stock <= 0) {
                $inventory->update(['status' => 'depleted']);
            } elseif ($inventory && $inventory->current_stock <= 10) {
                $inventory->update(['status' => 'low_stock']);
            }
        }

        // Increment coupon uses
        if ($this->appliedCouponCode) {
            FarmCoupon::where('company_id', $this->companyId)
                ->where('code', strtoupper($this->appliedCouponCode))
                ->increment('uses_count');
        }

        // Redeem loyalty points (debit ledger immediately on order placement)
        $customer = auth('shop_customer')->user();
        if ($customer && $this->pointsToRedeem > 0) {
            FarmLoyaltyPoint::redeem(
                $customer->id,
                $this->companyId,
                $this->pointsToRedeem,
                "Redeemed on order {$order->ref}",
                $order->id
            );
        }

        session()->forget('farm_shop_cart');

        // Clear abandoned cart tracking
        if ($customer) {
            FarmAbandonedCart::clearForCustomer($customer->id);
        }

        FarmOrderPlaced::dispatch($order);

        // B2B credit-term orders skip payment gateway — charged on invoice
        if ($this->isB2b && $this->b2bPaymentTerms !== 'prepay') {
            $order->update([
                'payment_status' => 'on_account',
                'status'         => 'confirmed',
            ]);
            // Track credit usage
            if ($order->b2b_account_id) {
                \Modules\Farms\Models\FarmB2bAccount::where('id', $order->b2b_account_id)
                    ->increment('credit_used', $order->total);
            }
            return $this->redirect(route('farm-shop.order.confirmation', $order));
        }

        return $this->redirect(route('farm-shop.order.payment', $order));
    }

    public function render()
    {
        $settings = $this->companyId
            ? app(ShopSettingsService::class)->get($this->companyId)
            : null;

        return view('farms::livewire.shop.checkout', [
            'shopName'             => $settings?->shop_name ?? 'Farm Shop',
            'effectiveDeliveryFee' => $this->getEffectiveDeliveryFee(),
            'orderTotal'           => $this->getOrderTotal(),
            'deliveryFee'          => $this->deliveryFee,
            'freeDeliveryAbove'    => $this->freeDeliveryAbove,
            'isB2b'                => $this->isB2b,
            'b2bDiscountPct'       => $this->b2bDiscountPct,
            'b2bDiscountAmt'       => $this->b2bDiscountAmt,
            'b2bPaymentTerms'      => $this->b2bPaymentTerms,
        ])->layout('farms::layouts.public', ['title' => 'Checkout']);
    }
}
