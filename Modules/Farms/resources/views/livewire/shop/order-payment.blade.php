<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="bg-white rounded-2xl shadow-sm p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Complete Payment</h1>
        <p class="text-gray-500 mb-8">Order <span class="font-semibold text-green-700">{{ $order->ref }}</span></p>

        {{-- Order Summary --}}
        <div class="bg-green-50 rounded-xl p-4 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600">{{ $order->items->count() }} item(s)</p>
                    <p class="text-sm text-gray-600 capitalize">{{ $order->delivery_type === 'pickup' ? '🚶 Pickup' : '🚚 Delivery' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-green-700">GHS {{ number_format($order->total, 2) }}</p>
                </div>
            </div>
        </div>

        {{-- Payment Methods --}}
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Select Payment Method</h3>

        @if(empty($groupedPaymentMethods))
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-800">
                No online payment methods are configured. Please contact us directly.
            </div>
        @else
            <div class="space-y-4 mb-8">
                @foreach($groupedPaymentMethods as $provider => $methods)
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">
                            {{ ucfirst($provider) }}
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($methods as $method)
                                <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all {{ $selectedPaymentMethod === ($method['code'] ?? '') ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300' }}">
                                    <input
                                        wire:model.live="selectedPaymentMethod"
                                        type="radio"
                                        value="{{ $method['code'] ?? '' }}"
                                        class="text-green-600 focus:ring-green-500"
                                    />
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ $method['name'] ?? ucfirst($method['code'] ?? $provider) }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <button
            wire:click="initiatePayment"
            wire:loading.attr="disabled"
            @if(empty($groupedPaymentMethods)) disabled @endif
            class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-semibold py-3.5 rounded-xl transition-colors flex items-center justify-center gap-2"
        >
            <span wire:loading.remove wire:target="initiatePayment">
                Pay GHS {{ number_format($order->total, 2) }}
            </span>
            <span wire:loading wire:target="initiatePayment">
                Redirecting to payment...
            </span>
        </button>

        <p class="text-xs text-center text-gray-400 mt-4">
            Secured by PaymentsChannel. Your payment details are encrypted.
        </p>
    </div>

</div>
