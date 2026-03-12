<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Checkout</h1>
            <p class="mt-0.5 text-sm text-gray-500">Review your book order before placing.</p>
        </div>
        <a href="{{ route('hostel_occupant.books.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors w-full sm:w-auto justify-center sm:justify-start">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Book Store
        </a>
    </div>

    @if($cartItems->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
            <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 mb-4">
                    <svg class="h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">Your cart is empty</p>
                <p class="mt-1 text-sm text-gray-500">Add books from the store before checking out.</p>
                <a href="{{ route('hostel_occupant.books.index') }}"
                   class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                    Browse Books
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
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Book</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit Price</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($cartItems as $line)
                            <tr>
                                <td class="px-5 py-3.5">
                                    <p class="text-sm font-medium text-gray-900">{{ $line['book']->title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">by {{ $line['book']->author }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                        {{ $line['book']->book_type === 'digital' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ ucfirst($line['book']->book_type) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="text-sm text-gray-700">{{ $line['qty'] }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="text-sm text-gray-700">GHS {{ number_format($line['book']->price, 2) }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="text-sm font-semibold text-gray-900">GHS {{ number_format($line['subtotal'], 2) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="4" class="px-5 py-3.5 text-right text-sm font-semibold text-gray-700">Total</td>
                            <td class="px-5 py-3.5 text-right text-base font-bold text-gray-900">GHS {{ number_format($total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- ── Delivery notes ──────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden p-5">
            <label for="books-checkout-notes" class="block text-sm font-medium text-gray-700 mb-1.5">
                Order Notes <span class="text-gray-400 font-normal">(optional)</span>
            </label>
            <textarea id="books-checkout-notes"
                      wire:model="notes"
                      rows="3"
                      placeholder="Delivery instructions, room number, or any special requests..."
                      class="block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 resize-none"></textarea>
        </div>

        {{-- ── Actions ─────────────────────────────────────────────────────── --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
            <a href="{{ route('hostel_occupant.books.index') }}"
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
