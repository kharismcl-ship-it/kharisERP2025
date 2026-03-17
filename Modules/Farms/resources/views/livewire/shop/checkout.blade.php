<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="text-2xl font-bold text-gray-900 mb-8">Checkout</h1>

    {{-- Step Indicator --}}
    <div class="flex items-center mb-10">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $step >= 1 ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500' }}">1</div>
            <span class="text-sm font-medium {{ $step >= 1 ? 'text-green-700' : 'text-gray-400' }}">Your Details</span>
        </div>
        <div class="flex-1 h-0.5 mx-4 {{ $step >= 2 ? 'bg-green-500' : 'bg-gray-200' }}"></div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $step >= 2 ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500' }}">2</div>
            <span class="text-sm font-medium {{ $step >= 2 ? 'text-green-700' : 'text-gray-400' }}">Review & Pay</span>
        </div>
    </div>

    @if($step === 1)
        {{-- Step 1: Customer Details --}}
        <div class="bg-white rounded-2xl shadow-sm p-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Contact & Delivery Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input wire:model="customerName" type="text" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" placeholder="Your name" />
                    @error('customerName') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                    <input wire:model="customerPhone" type="tel" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" placeholder="0XX XXX XXXX" />
                    @error('customerPhone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input wire:model="customerEmail" type="email" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" placeholder="optional — for order updates" />
                    @error('customerEmail') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Method *</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex cursor-pointer rounded-xl border-2 p-4 {{ $deliveryType === 'pickup' ? 'border-green-500 bg-green-50' : 'border-gray-200' }}">
                            <input wire:model.live="deliveryType" type="radio" value="pickup" class="sr-only" />
                            <div>
                                <p class="font-semibold text-gray-900">🚶 Pickup</p>
                                <p class="text-xs text-gray-500 mt-0.5">Collect from our farm</p>
                                <p class="text-xs font-semibold text-green-700 mt-1">Free</p>
                            </div>
                        </label>
                        <label class="relative flex cursor-pointer rounded-xl border-2 p-4 {{ $deliveryType === 'delivery' ? 'border-green-500 bg-green-50' : 'border-gray-200' }}">
                            <input wire:model.live="deliveryType" type="radio" value="delivery" class="sr-only" />
                            <div>
                                <p class="font-semibold text-gray-900">🚚 Delivery</p>
                                <p class="text-xs text-gray-500 mt-0.5">Delivered to you</p>
                                <p class="text-xs font-semibold text-green-700 mt-1">GHS {{ number_format($deliveryFee, 2) }}</p>
                                @if($freeDeliveryAbove)
                                    <p class="text-xs text-gray-400 mt-0.5">Free above GHS {{ number_format($freeDeliveryAbove, 2) }}</p>
                                @endif
                            </div>
                        </label>
                    </div>
                </div>

                @if($deliveryType === 'delivery')
                    {{-- Saved address picker (logged-in customers only) --}}
                    @if(auth('shop_customer')->check() && count($savedAddresses) > 0)
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Saved Addresses</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3">
                            @foreach($savedAddresses as $sa)
                            <button type="button"
                                wire:click="selectSavedAddress({{ $sa->id }})"
                                class="text-left border-2 rounded-xl px-4 py-3 transition-colors
                                    {{ $deliveryAddress === $sa->address ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300' }}">
                                <p class="font-medium text-sm text-gray-900">{{ $sa->label }}</p>
                                <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $sa->address }}</p>
                                @if($sa->landmark)
                                    <p class="text-xs text-gray-400 mt-0.5 truncate">📍 {{ $sa->landmark }}</p>
                                @endif
                            </button>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400 mb-1">Or enter a new address below:</p>
                    </div>
                    @endif

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address *</label>
                        <textarea wire:model="deliveryAddress" rows="2" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" placeholder="Street, Town, City, Region"></textarea>
                        @error('deliveryAddress') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nearest Landmark</label>
                        <input wire:model="deliveryLandmark" type="text" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" placeholder="e.g. Near Shell Station, Opposite Melcom, After the roundabout" />
                        <p class="mt-1 text-xs text-gray-400">Helps our driver find you easily</p>
                    </div>

                    {{-- Delivery Slot Picker --}}
                    @if(count($availableDeliverySlots) > 0)
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Preferred Delivery Date *
                                @if($cutoffTime)
                                    <span class="text-xs text-gray-400 font-normal">(order by {{ $cutoffTime }} for same-day processing)</span>
                                @endif
                            </label>
                            <select wire:model="preferredDeliveryDate"
                                class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                                <option value="">— Select a delivery date —</option>
                                @foreach($availableDeliverySlots as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('preferredDeliveryDate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endif
                @endif

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Notes</label>
                    <textarea wire:model="notes" rows="2" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" placeholder="Any special instructions?"></textarea>
                </div>

                {{-- B2B PO Number --}}
                @if($isB2b)
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Purchase Order (PO) Number <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <input wire:model="poNumber" type="text"
                           class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                           placeholder="Your internal PO reference" />
                    @error('poNumber')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                @endif
            </div>

            <div class="mt-8 flex justify-between">
                <a href="{{ route('farm-shop.cart') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium">← Back to Cart</a>
                <button wire:click="nextStep" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-8 rounded-xl transition-colors">
                    Continue →
                </button>
            </div>
        </div>

    @elseif($step === 2)
        {{-- Step 2: Review --}}
        <div class="bg-white rounded-2xl shadow-sm p-8 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Order Summary</h2>

            <div class="space-y-3 mb-6">
                @foreach($cartItems as $item)
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-900">{{ $item['product_name'] }}</p>
                            <p class="text-xs text-gray-400">{{ $item['quantity'] }} {{ $item['unit'] }} × GHS {{ number_format($item['unit_price'], 2) }}</p>
                        </div>
                        <span class="font-semibold text-gray-900">GHS {{ number_format($item['subtotal'], 2) }}</span>
                    </div>
                @endforeach
            </div>

            <div class="border-t pt-4 space-y-2">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span>GHS {{ number_format($cartTotal, 2) }}</span>
                </div>
                @if($deliveryType === 'delivery')
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Delivery Fee</span>
                        <span>{{ $effectiveDeliveryFee == 0 ? '🎉 Free' : 'GHS ' . number_format($effectiveDeliveryFee, 2) }}</span>
                    </div>
                @endif
                @if($discountAmount > 0)
                    <div class="flex justify-between text-sm text-green-700">
                        <span>Coupon ({{ strtoupper($appliedCouponCode) }}) 🎟️</span>
                        <span>− GHS {{ number_format($discountAmount, 2) }}</span>
                    </div>
                @endif
                @if($loyaltyDiscount > 0)
                    <div class="flex justify-between text-sm text-amber-700">
                        <span>⭐ Loyalty Points ({{ number_format($pointsToRedeem) }} pts)</span>
                        <span>− GHS {{ number_format($loyaltyDiscount, 2) }}</span>
                    </div>
                @endif
                @if($isB2b && $b2bDiscountAmt > 0)
                    <div class="flex justify-between text-sm text-blue-700 font-medium">
                        <span>🏢 Wholesale Discount ({{ $b2bDiscountPct }}%)</span>
                        <span>− GHS {{ number_format($b2bDiscountAmt, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t">
                    <span>Total</span>
                    <span class="text-green-700">GHS {{ number_format($orderTotal, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- B2B wholesale notice --}}
        @if($isB2b)
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-6 flex items-start gap-3">
            <span class="text-2xl flex-shrink-0">🏢</span>
            <div>
                <p class="font-semibold text-blue-900 text-sm">Wholesale Account Active</p>
                <p class="text-xs text-blue-700">
                    {{ $b2bDiscountPct }}% wholesale discount applied.
                    @if($b2bPaymentTerms !== 'prepay')
                        Payment terms: <strong>{{ match($b2bPaymentTerms) { 'net7' => 'Net 7 days', 'net14' => 'Net 14 days', 'net30' => 'Net 30 days', default => $b2bPaymentTerms } }}</strong> — this order will be invoiced to your account.
                    @else
                        Payment required at checkout.
                    @endif
                </p>
            </div>
        </div>
        @endif

        {{-- Loyalty Points Redemption --}}
        @if($loyaltyEnabled && auth('shop_customer')->check() && $loyaltyBalance > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">⭐</span>
                    <div>
                        <p class="font-semibold text-amber-900">{{ number_format($loyaltyBalance) }} Loyalty Points Available</p>
                        <p class="text-xs text-amber-700">Worth up to GHS {{ number_format($loyaltyBalance * $loyaltyValuePerPoint, 2) }} off your order</p>
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model.live="redeemLoyalty" type="checkbox" class="rounded border-amber-400 text-amber-600 focus:ring-amber-500" />
                    <span class="text-sm font-medium text-amber-800">Use Points</span>
                </label>
            </div>
            @if($redeemLoyalty && $pointsToRedeem > 0)
                <div class="mt-3 pt-3 border-t border-amber-200 flex justify-between text-sm">
                    <span class="text-amber-800">{{ number_format($pointsToRedeem) }} points redeemed</span>
                    <span class="font-bold text-amber-900">− GHS {{ number_format($loyaltyDiscount, 2) }}</span>
                </div>
            @endif
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <h3 class="font-semibold text-gray-900 mb-3">Delivery Details</h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><span class="text-gray-500">Name:</span> <span class="font-medium">{{ $customerName }}</span></div>
                <div><span class="text-gray-500">Phone:</span> <span class="font-medium">{{ $customerPhone }}</span></div>
                @if($customerEmail)
                    <div><span class="text-gray-500">Email:</span> <span class="font-medium">{{ $customerEmail }}</span></div>
                @endif
                <div><span class="text-gray-500">Method:</span>
                    <span class="font-medium">{{ $deliveryType === 'pickup' ? '🚶 Pickup' : '🚚 Delivery' }}</span>
                </div>
                @if($deliveryType === 'delivery' && $deliveryAddress)
                    <div class="col-span-2"><span class="text-gray-500">Address:</span> <span class="font-medium">{{ $deliveryAddress }}</span></div>
                @endif
                @if($deliveryType === 'delivery' && $deliveryLandmark)
                    <div class="col-span-2"><span class="text-gray-500">Landmark:</span> <span class="font-medium">{{ $deliveryLandmark }}</span></div>
                @endif
                @if($preferredDeliveryDate)
                    <div class="col-span-2">
                        <span class="text-gray-500">Delivery Date:</span>
                        <span class="font-medium">{{ \Carbon\Carbon::parse($preferredDeliveryDate)->format('D, M j, Y') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex justify-between items-center">
            <button wire:click="prevStep" class="text-sm text-gray-500 hover:text-gray-700 font-medium">← Edit Details</button>
            <button wire:click="placeOrder" wire:loading.attr="disabled" class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-semibold py-3 px-8 rounded-xl transition-colors flex items-center gap-2">
                <span wire:loading.remove wire:target="placeOrder">Place Order & Pay</span>
                <span wire:loading wire:target="placeOrder">Placing Order...</span>
            </button>
        </div>
    @endif

</div>
