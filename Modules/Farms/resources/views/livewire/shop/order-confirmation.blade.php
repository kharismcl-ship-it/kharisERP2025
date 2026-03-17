<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Success Banner --}}
    <div class="text-center mb-10">
        <div class="text-7xl mb-4">🎉</div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Confirmed!</h1>
        <p class="text-gray-500">Thank you, <strong>{{ $order->customer_name }}</strong>! Your order has been received.</p>
    </div>

    {{-- Order Reference --}}
    <div class="bg-green-50 border border-green-200 rounded-2xl p-6 text-center mb-6">
        <p class="text-sm text-gray-500 mb-1">Your Order Reference</p>
        <p class="text-3xl font-bold text-green-700 tracking-wider">{{ $order->ref }}</p>
        <p class="text-xs text-gray-400 mt-2">Save this to track your order</p>
    </div>

    {{-- Order Items --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h2 class="font-semibold text-gray-900 mb-4">What you ordered</h2>
        <div class="space-y-3">
            @foreach($order->items as $item)
                <div class="flex justify-between items-center text-sm">
                    <div>
                        <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                        <p class="text-gray-400">{{ $item->quantity }} {{ $item->unit }}</p>
                    </div>
                    <span class="font-semibold text-gray-900">GHS {{ number_format($item->subtotal, 2) }}</span>
                </div>
            @endforeach
        </div>
        <div class="border-t mt-4 pt-4">
            <div class="flex justify-between text-sm text-gray-600">
                <span>Subtotal</span>
                <span>GHS {{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->delivery_fee > 0)
                <div class="flex justify-between text-sm text-gray-600 mt-1">
                    <span>Delivery Fee</span>
                    <span>GHS {{ number_format($order->delivery_fee, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between font-bold text-gray-900 mt-2 pt-2 border-t text-base">
                <span>Total Paid</span>
                <span class="text-green-700">GHS {{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Delivery Info --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
        <h2 class="font-semibold text-gray-900 mb-4">Delivery Details</h2>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div><span class="text-gray-500">Method:</span>
                <span class="font-medium ml-1">{{ $order->delivery_type === 'pickup' ? '🚶 Farm Pickup' : '🚚 Delivery' }}</span>
            </div>
            @if($order->customer_email)
                <div><span class="text-gray-500">Email:</span>
                    <span class="font-medium ml-1">{{ $order->customer_email }}</span>
                </div>
            @endif
            <div><span class="text-gray-500">Phone:</span>
                <span class="font-medium ml-1">{{ $order->customer_phone }}</span>
            </div>
            @if($order->delivery_address)
                <div class="col-span-2"><span class="text-gray-500">Address:</span>
                    <span class="font-medium ml-1">{{ $order->delivery_address }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('farm-shop.track') }}" class="text-center bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-medium py-3 px-6 rounded-xl transition-colors">
            Track Your Order
        </a>
        <a href="{{ route('farm-shop.index') }}" class="text-center bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-xl transition-colors">
            Continue Shopping
        </a>
    </div>

</div>
