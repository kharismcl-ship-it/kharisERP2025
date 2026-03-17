<div class="min-h-screen bg-gray-50 py-8 px-4">

    {{-- Print / Back buttons --}}
    <div class="max-w-2xl mx-auto mb-4 flex items-center justify-between no-print">
        <a href="{{ route('farm-shop.my-orders') }}"
            class="text-sm font-medium text-gray-500 hover:text-gray-700">
            ← Back to Orders
        </a>
        <button onclick="window.print()"
            class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            🖨 Print Receipt
        </button>
    </div>

    {{-- Receipt Card --}}
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm receipt-card overflow-hidden">

        {{-- Header --}}
        <div class="bg-green-700 text-white px-8 py-6 text-center">
            @if($settings?->shop_name)
                <h1 class="text-xl font-bold">{{ $settings->shop_name }}</h1>
            @endif
            @if($settings?->tagline)
                <p class="text-green-200 text-sm mt-0.5">{{ $settings->tagline }}</p>
            @endif
            <p class="text-green-100 text-xs mt-2">
                @if($settings?->phone) {{ $settings->phone }} @endif
                @if($settings?->email)  · {{ $settings->email }} @endif
            </p>
        </div>

        <div class="px-8 py-6">

            {{-- Order ref + status --}}
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Order Reference</p>
                    <p class="text-2xl font-bold text-gray-900 font-mono">{{ $this->order->ref }}</p>
                    <p class="text-sm text-gray-400 mt-1">{{ $this->order->placed_at?->format('D, M d, Y · H:i') }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ match($this->order->status) {
                            'delivered' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                            'processing', 'ready' => 'bg-blue-100 text-blue-800',
                            default => 'bg-yellow-100 text-yellow-800',
                        } }}">
                        {{ ucfirst($this->order->status) }}
                    </span>
                    <p class="text-xs mt-1.5
                        {{ $this->order->payment_status === 'paid' ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                        {{ $this->order->payment_status === 'paid' ? '✓ Paid' : ucfirst($this->order->payment_status) }}
                    </p>
                </div>
            </div>

            {{-- Customer info --}}
            <div class="mb-6 pb-4 border-b border-gray-100">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Customer</p>
                <p class="font-semibold text-gray-900">{{ $this->order->customer_name }}</p>
                <p class="text-sm text-gray-500">{{ $this->order->customer_phone }}</p>
                @if($this->order->customer_email)
                    <p class="text-sm text-gray-500">{{ $this->order->customer_email }}</p>
                @endif
            </div>

            {{-- Delivery info --}}
            <div class="mb-6 pb-4 border-b border-gray-100">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Delivery</p>
                <p class="text-sm text-gray-700">
                    {{ $this->order->delivery_type === 'pickup' ? '🚶 Pickup' : '🚚 Delivery' }}
                </p>
                @if($this->order->delivery_address)
                    <p class="text-sm text-gray-500 mt-0.5">{{ $this->order->delivery_address }}</p>
                @endif
                @if($this->order->delivery_landmark)
                    <p class="text-sm text-gray-400 mt-0.5">Landmark: {{ $this->order->delivery_landmark }}</p>
                @endif
                @if($this->order->preferred_delivery_date)
                    <p class="text-sm text-gray-500 mt-1">Scheduled: {{ $this->order->preferred_delivery_date->format('D, M d, Y') }}</p>
                @endif
            </div>

            {{-- Line items --}}
            <div class="mb-6">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-3">Items</p>
                <div class="space-y-2">
                    @foreach($this->order->items as $item)
                    <div class="flex items-center justify-between text-sm">
                        <div>
                            <span class="font-medium text-gray-900">{{ $item->product_name }}</span>
                            <span class="text-gray-400 ml-1">× {{ $item->quantity }} {{ $item->unit }}</span>
                        </div>
                        <span class="text-gray-700 font-medium">GHS {{ number_format($item->subtotal, 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Totals --}}
            <div class="border-t border-gray-100 pt-4 space-y-2">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span>GHS {{ number_format($this->order->subtotal, 2) }}</span>
                </div>
                @if($this->order->delivery_fee > 0)
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Delivery Fee</span>
                    <span>GHS {{ number_format($this->order->delivery_fee, 2) }}</span>
                </div>
                @endif
                @if($this->order->discount_amount > 0)
                <div class="flex justify-between text-sm text-green-700">
                    <span>Discount @if($this->order->coupon_code)({{ $this->order->coupon_code }})@endif</span>
                    <span>− GHS {{ number_format($this->order->discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-gray-100">
                    <span>Total</span>
                    <span>GHS {{ number_format($this->order->total, 2) }}</span>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="bg-gray-50 px-8 py-4 text-center">
            <p class="text-xs text-gray-400">Thank you for shopping with us!</p>
            @if($settings?->address)
                <p class="text-xs text-gray-400 mt-0.5">{{ $settings->address }}</p>
            @endif
        </div>
    </div>

</div>
