<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="text-2xl font-bold text-gray-900 mb-8">🛒 Your Cart</h1>

    @if(empty($items))
        <div class="text-center py-20 bg-white rounded-2xl shadow-sm">
            <div class="text-6xl mb-4">🛒</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Your cart is empty</h3>
            <p class="text-gray-500 mb-6">Add some fresh produce from the shop!</p>
            <a href="{{ route('farm-shop.index') }}" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors">
                Browse Shop
            </a>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide border-b">
                        <th class="px-6 py-4 text-left">Product</th>
                        <th class="px-4 py-4 text-center">Qty</th>
                        <th class="px-4 py-4 text-right">Price</th>
                        <th class="px-4 py-4 text-right">Subtotal</th>
                        <th class="px-4 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($items as $inventoryId => $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $item['product_name'] }}</div>
                                <div class="text-xs text-gray-400">{{ $item['farm_name'] }}</div>
                                @if(! empty($item['tier_label']))
                                    <span class="text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-medium">{{ $item['tier_label'] }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <input
                                    type="number"
                                    min="0.1"
                                    step="0.1"
                                    value="{{ $item['quantity'] }}"
                                    wire:change="updateQuantity({{ $inventoryId }}, $event.target.value)"
                                    class="w-20 text-center rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"
                                />
                                <div class="text-xs text-gray-400 mt-0.5">{{ $item['unit'] }}</div>
                            </td>
                            <td class="px-4 py-4 text-right text-sm text-gray-600">
                                GHS {{ number_format($item['unit_price'], 2) }}
                            </td>
                            <td class="px-4 py-4 text-right font-semibold text-gray-900">
                                GHS {{ number_format($item['subtotal'], 2) }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <button wire:click="removeItem({{ $inventoryId }})" class="text-red-400 hover:text-red-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Coupon Code --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-4">
            <p class="text-sm font-medium text-gray-700 mb-3">🎟️ Have a coupon code?</p>
            @if($appliedCoupon)
                <div class="flex items-center justify-between rounded-xl bg-green-50 border border-green-200 px-4 py-3">
                    <div>
                        <span class="font-mono font-bold text-green-800">{{ $appliedCoupon['code'] }}</span>
                        <span class="ml-2 text-sm text-green-700">— {{ $appliedCoupon['label'] }} (−GHS {{ number_format($appliedCoupon['discount_amount'], 2) }})</span>
                    </div>
                    <button wire:click="removeCoupon" class="text-xs text-red-500 hover:text-red-700 font-medium ml-4">Remove</button>
                </div>
            @else
                <div class="flex gap-2">
                    <input wire:model="couponInput" type="text"
                        placeholder="Enter coupon code"
                        class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 uppercase text-sm"
                        style="text-transform: uppercase" />
                    <button wire:click="applyCoupon"
                        wire:loading.attr="disabled"
                        class="bg-gray-800 hover:bg-gray-900 disabled:opacity-50 text-white text-sm font-semibold py-2 px-5 rounded-xl transition-colors">
                        Apply
                    </button>
                </div>
                @if($couponError)
                    <p class="mt-2 text-xs text-red-600">{{ $couponError }}</p>
                @endif
            @endif
        </div>

        {{-- Summary --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-4">
            <div class="space-y-2">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span>GHS {{ number_format($cartTotal, 2) }}</span>
                </div>
                @if($appliedCoupon)
                    <div class="flex justify-between text-sm text-green-700">
                        <span>Coupon discount</span>
                        <span>− GHS {{ number_format($appliedCoupon['discount_amount'], 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t">
                    <span>Total (excl. delivery)</span>
                    <span class="text-green-700">GHS {{ number_format(max(0, $cartTotal - ($appliedCoupon['discount_amount'] ?? 0)), 2) }}</span>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-1">Delivery fee calculated at checkout</p>
        </div>

        <div class="flex justify-between items-center">
            <button wire:click="clearCart" class="text-sm text-red-500 hover:text-red-700 font-medium transition-colors">
                Clear Cart
            </button>
            <button wire:click="proceedToCheckout" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-xl transition-colors">
                Proceed to Checkout →
            </button>
        </div>
    @endif

</div>
