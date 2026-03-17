<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Orders</h1>
            <p class="text-sm text-gray-500 mt-1">Welcome back, {{ auth('shop_customer')->user()?->name }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('farm-shop.my-wishlist') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">♡ Wishlist</a>
            <a href="{{ route('farm-shop.my-account') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">My Account</a>
            <a href="{{ route('farm-shop.index') }}" class="text-sm font-medium text-green-700 hover:text-green-900">← Continue Shopping</a>
        </div>
    </div>

    @if($loyaltyEnabled && $loyaltyBalance > 0)
        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl px-5 py-3 flex items-center gap-3">
            <span class="text-2xl">⭐</span>
            <div>
                <p class="font-semibold text-amber-900">{{ number_format($loyaltyBalance) }} Loyalty Points</p>
                <p class="text-xs text-amber-700">Redeem on your next order at checkout for a discount</p>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if($orders->count())
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-4">
                        <div>
                            <p class="font-semibold text-gray-900 font-mono">{{ $order->ref }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $order->placed_at?->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        {{-- Order status badge --}}
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ match($order->status) {
                                'delivered' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                'processing','ready' => 'bg-blue-100 text-blue-800',
                                default => 'bg-yellow-100 text-yellow-800',
                            } }}">
                            {{ ucfirst($order->status) }}
                        </span>
                        {{-- Payment badge --}}
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $order->payment_status === 'paid' ? '✓ Paid' : ucfirst($order->payment_status) }}
                        </span>
                        <span class="font-bold text-gray-900">GHS {{ number_format($order->total, 2) }}</span>
                    </div>
                </div>

                <div class="px-6 py-3">
                    <div class="text-sm text-gray-500 space-y-0.5">
                        @foreach($order->items as $item)
                            <span class="mr-3">{{ $item->product_name }} <span class="text-gray-400">×{{ $item->quantity }} {{ $item->unit }}</span></span>
                        @endforeach
                    </div>
                </div>

                <div class="px-6 pb-4 flex items-center justify-between">
                    <div class="text-xs text-gray-400">
                        {{ $order->delivery_type === 'pickup' ? '🚶 Pickup' : '🚚 Delivery' }}
                        @if($order->delivery_address) · {{ Str::limit($order->delivery_address, 50) }} @endif
                    </div>
                    <div class="flex items-center gap-3">
                        {{-- Re-order button (delivered/cancelled orders) --}}
                        @if(in_array($order->status, ['delivered', 'cancelled']))
                            <button wire:click="reOrder({{ $order->id }})"
                                wire:loading.attr="disabled"
                                class="text-sm font-medium text-green-700 hover:text-green-900 disabled:opacity-50">
                                🔄 Re-order
                            </button>
                        @endif

                        {{-- Refund request (paid, non-cancelled orders) --}}
                        @if($order->payment_status === 'paid' && in_array($order->status, ['confirmed', 'processing', 'ready', 'delivered']))
                            <a href="{{ route('farm-shop.order.refund', $order) }}"
                                class="text-sm font-medium text-orange-600 hover:text-orange-800">
                                ↩ Refund
                            </a>
                        @endif

                        {{-- Cancel button (pending orders only) --}}
                        @if($order->status === 'pending')
                            <button
                                wire:click="cancelOrder({{ $order->id }})"
                                wire:confirm="Cancel order {{ $order->ref }}? This cannot be undone."
                                wire:loading.attr="disabled"
                                class="text-sm font-medium text-red-600 hover:text-red-800 disabled:opacity-50">
                                ✕ Cancel
                            </button>
                        @endif

                        {{-- Receipt (paid orders) --}}
                        @if($order->payment_status === 'paid')
                            <a href="{{ route('farm-shop.order.receipt', $order) }}"
                                target="_blank"
                                class="text-sm font-medium text-gray-500 hover:text-gray-700">
                                🖨 Receipt
                            </a>
                        @endif

                        <a href="{{ route('farm-shop.track') }}?ref={{ $order->ref }}&phone={{ $order->customer_phone }}"
                            class="text-sm font-medium text-green-700 hover:text-green-900">
                            Track →
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm p-16 text-center">
            <div class="text-6xl mb-4">🛒</div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No orders yet</h3>
            <p class="text-gray-500 mb-6">Start shopping to see your orders here.</p>
            <a href="{{ route('farm-shop.index') }}"
                class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-colors">
                Browse Products
            </a>
        </div>
    @endif
</div>
