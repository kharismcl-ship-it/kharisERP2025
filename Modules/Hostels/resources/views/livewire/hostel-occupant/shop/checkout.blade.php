<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Checkout</h1>
            <p class="mt-0.5 text-sm text-gray-500">Review your order before placing.</p>
        </div>
        <a href="{{ route('hostel_occupant.shop.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors w-full sm:w-auto justify-center sm:justify-start">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Shop
        </a>
    </div>

    @if($cartItems->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
            <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 mb-4">
                    <svg class="h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">Your cart is empty</p>
                <p class="mt-1 text-sm text-gray-500">Add items from the shop before checking out.</p>
                <a href="{{ route('hostel_occupant.shop.index') }}"
                   class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                    Go to Shop
                </a>
            </div>
        </div>
    @else

        {{-- ── Order summary ───────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-gray-700">Order Summary</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit Price</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($cartItems as $line)
                            <tr>
                                <td class="px-5 py-3.5">
                                    <p class="text-sm font-medium text-gray-900">{{ $line['name'] }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="text-sm text-gray-700">{{ $line['qty'] }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="text-sm text-gray-700">GHS {{ number_format($line['price'], 2) }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="text-sm font-semibold text-gray-900">GHS {{ number_format($line['subtotal'], 2) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="3" class="px-5 py-3.5 text-right text-sm font-semibold text-gray-700">Total</td>
                            <td class="px-5 py-3.5 text-right text-base font-bold text-gray-900">GHS {{ number_format($total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- ── Delivery notes ──────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden p-5">
            <label for="checkout-notes" class="block text-sm font-medium text-gray-700 mb-1.5">
                Delivery Instructions <span class="text-gray-400 font-normal">(optional)</span>
            </label>
            <textarea id="checkout-notes"
                      wire:model="notes"
                      rows="3"
                      placeholder="e.g. Room 204, leave at the door..."
                      class="block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 resize-none"></textarea>
        </div>

        {{-- ── Actions ─────────────────────────────────────────────────────── --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
            <a href="{{ route('hostel_occupant.shop.index') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors w-full sm:w-auto">
                Continue Shopping
            </a>
            <button wire:click="placeOrder"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70 cursor-not-allowed"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors w-full sm:w-auto">
                <svg class="h-4 w-4" wire:loading.remove wire:target="placeOrder" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span wire:loading.remove wire:target="placeOrder">Place Order — GHS {{ number_format($total, 2) }}</span>
                <span wire:loading wire:target="placeOrder">Placing order...</span>
            </button>
        </div>

    @endif

</div>
