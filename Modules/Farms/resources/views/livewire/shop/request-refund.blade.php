<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <a href="{{ route('farm-shop.my-orders') }}"
        class="inline-flex items-center gap-1.5 text-green-700 hover:text-green-900 text-sm font-medium mb-6">
        ← Back to My Orders
    </a>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900">Request Refund</h1>
            <p class="text-sm text-gray-500 mt-1">Order <span class="font-mono font-semibold">{{ $order->ref }}</span>
                · GHS {{ number_format($order->total, 2) }}</p>
        </div>

        <div class="px-6 py-6">

            @if($submitted)
                <div class="text-center py-8">
                    <div class="text-5xl mb-4">✅</div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Refund Request Submitted</h2>
                    <p class="text-gray-500 mb-6">We have received your request and will review it within 2–3 business days.
                        You will be notified by phone or email once a decision is made.</p>
                    <a href="{{ route('farm-shop.my-orders') }}"
                        class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-colors">
                        Back to My Orders
                    </a>
                </div>
            @else
                {{-- Order summary --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Items in this order</p>
                    <div class="space-y-1">
                        @foreach($order->items as $item)
                            <div class="flex justify-between text-sm text-gray-700">
                                <span>{{ $item->product_name }} × {{ $item->quantity }} {{ $item->unit }}</span>
                                <span class="font-medium">GHS {{ number_format($item->subtotal, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Refund reason --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Reason for Refund <span class="text-red-500">*</span></label>
                    <select wire:model="reason"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">Select a reason...</option>
                        @foreach(\Modules\Farms\Models\FarmReturnRequest::REASONS as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('reason') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Additional Details (optional)</label>
                    <textarea wire:model="description" rows="4"
                        placeholder="Please describe the issue in detail to help us process your request faster..."
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3">
                    <button wire:click="submit"
                        wire:loading.attr="disabled"
                        class="flex-1 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-semibold py-3 rounded-xl transition-colors">
                        <span wire:loading.remove wire:target="submit">Submit Refund Request</span>
                        <span wire:loading wire:target="submit">Submitting...</span>
                    </button>
                    <a href="{{ route('farm-shop.my-orders') }}"
                        class="px-6 py-3 text-gray-500 hover:text-gray-700 font-medium rounded-xl border border-gray-200 hover:border-gray-300 transition-colors">
                        Cancel
                    </a>
                </div>

                <p class="mt-4 text-xs text-gray-400">
                    Refund decisions are communicated within 2–3 business days. Approved refunds are processed within 5–7 business days.
                </p>
            @endif

        </div>
    </div>

</div>
