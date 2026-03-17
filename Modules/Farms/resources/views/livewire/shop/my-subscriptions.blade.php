<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Subscriptions</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your recurring farm orders</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('farm-shop.my-orders') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">My Orders</a>
            <a href="{{ route('farm-shop.index') }}" class="text-sm font-medium text-green-700 hover:text-green-900">← Shop</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    @if($subscriptions->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-16 text-center">
            <div class="text-6xl mb-4">🔄</div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No active subscriptions</h3>
            <p class="text-gray-500 mb-6">Subscribe to your favourite products for regular automatic deliveries.</p>
            <a href="{{ route('farm-shop.index') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-colors">
                Browse Products
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($subscriptions as $sub)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-900">
                                {{ \Modules\Farms\Models\FarmSubscription::FREQUENCIES[$sub->frequency] ?? ucfirst($sub->frequency) }} Box
                            </span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-medium
                                {{ match($sub->status) {
                                    'active'    => 'bg-green-100 text-green-800',
                                    'paused'    => 'bg-yellow-100 text-yellow-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    default     => 'bg-gray-100 text-gray-600',
                                } }}">
                                {{ ucfirst($sub->status) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-400 mt-0.5">
                            @if($sub->status === 'active')
                                Next order: <span class="font-medium text-gray-600">{{ $sub->next_order_date->format('D, M j, Y') }}</span>
                                ({{ $sub->next_order_date->diffForHumans() }})
                            @elseif($sub->status === 'paused')
                                Paused on {{ $sub->paused_at?->format('M j, Y') }}
                            @else
                                Cancelled on {{ $sub->cancelled_at?->format('M j, Y') }}
                            @endif
                        </p>
                    </div>
                    <span class="font-bold text-gray-900">GHS {{ number_format($sub->subtotal, 2) }} / delivery</span>
                </div>

                <div class="px-6 py-3">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Items</p>
                    <div class="space-y-1">
                        @foreach($sub->items as $item)
                            <div class="flex justify-between text-sm text-gray-700">
                                <span>{{ $item['product_name'] }} <span class="text-gray-400">× {{ $item['quantity'] }} {{ $item['unit'] }}</span></span>
                                <span class="text-gray-500">GHS {{ number_format($item['unit_price'], 2) }}/{{ $item['unit'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($sub->delivery_type === 'delivery' && $sub->delivery_address)
                    <div class="px-6 py-2 text-xs text-gray-400 border-t border-gray-50">
                        🚚 Delivery to: {{ $sub->delivery_address }}
                        @if($sub->delivery_landmark) · {{ $sub->delivery_landmark }} @endif
                    </div>
                @else
                    <div class="px-6 py-2 text-xs text-gray-400 border-t border-gray-50">🚶 Pickup</div>
                @endif

                @if(in_array($sub->status, ['active', 'paused']))
                <div class="px-6 pb-4 pt-2 flex gap-3">
                    @if($sub->status === 'active')
                        <button wire:click="pauseSubscription({{ $sub->id }})"
                            wire:confirm="Pause this subscription?"
                            class="text-sm font-medium text-yellow-700 hover:text-yellow-900 border border-yellow-300 px-3 py-1.5 rounded-lg">
                            ⏸ Pause
                        </button>
                    @else
                        <button wire:click="resumeSubscription({{ $sub->id }})"
                            class="text-sm font-medium text-green-700 hover:text-green-900 border border-green-300 px-3 py-1.5 rounded-lg">
                            ▶ Resume
                        </button>
                    @endif
                    <button wire:click="cancelSubscription({{ $sub->id }})"
                        wire:confirm="Cancel this subscription? This cannot be undone."
                        class="text-sm font-medium text-red-600 hover:text-red-800">
                        ✕ Cancel
                    </button>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    @endif

</div>
