<div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="bg-white rounded-2xl shadow-sm p-10 text-center">
        @if($paymentStatus === 'failed')
            <div class="text-6xl mb-4">❌</div>
            <h1 class="text-2xl font-bold text-gray-900 mb-3">Payment Failed</h1>
            <p class="text-gray-500 mb-8">{{ $message }}</p>

            <a href="{{ route('farm-shop.order.payment', $order) }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-xl transition-colors">
                Try Again
            </a>
        @else
            <div class="text-6xl mb-4">⏳</div>
            <h1 class="text-2xl font-bold text-gray-900 mb-3">Verifying Payment</h1>
            <p class="text-gray-500">Please wait while we confirm your payment...</p>
        @endif
    </div>
</div>
