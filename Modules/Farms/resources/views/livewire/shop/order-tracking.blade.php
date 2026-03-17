<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="text-center mb-10">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">📦 Track Your Order</h1>
        <p class="text-gray-500">Enter your order reference and phone number to check your order status.</p>
    </div>

    {{-- Search Form --}}
    <div class="bg-white rounded-2xl shadow-sm p-8 mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Order Reference</label>
                <input
                    wire:model="ref"
                    type="text"
                    placeholder="ORD-202603-00001"
                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 uppercase"
                />
                @error('ref') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input
                    wire:model="phone"
                    type="tel"
                    placeholder="0XX XXX XXXX"
                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                />
                @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <button wire:click="track" wire:loading.attr="disabled" class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-semibold py-3 rounded-xl transition-colors">
            <span wire:loading.remove>Track Order</span>
            <span wire:loading>Searching...</span>
        </button>
    </div>

    @if($error)
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700 mb-6">
            {{ $error }}
        </div>
    @endif

    @if($order)
        <div class="bg-white rounded-2xl shadow-sm p-8">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Order Reference</p>
                    <p class="text-xl font-bold text-green-700">{{ $order->ref }}</p>
                </div>
                <div class="text-right">
                    @php
                        $color = $this->statusColor($order->status);
                        $label = $this->statusLabel($order->status);
                    @endphp
                    <span class="inline-block bg-{{ $color }}-100 text-{{ $color }}-800 text-xs font-semibold px-3 py-1.5 rounded-full">
                        {{ $label }}
                    </span>
                </div>
            </div>

            {{-- Progress --}}
            @php
                $stages = ['pending', 'confirmed', 'processing', 'ready', 'delivered'];
                $currentIdx = array_search($order->status, $stages) ?? -1;
            @endphp
            @if($order->status !== 'cancelled')
                <div class="flex items-center mb-8">
                    @foreach($stages as $i => $stage)
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $i <= $currentIdx ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-400' }}">
                                {{ $i < $currentIdx ? '✓' : $i + 1 }}
                            </div>
                            <p class="text-xs text-gray-400 mt-1 w-14 text-center capitalize">{{ str_replace('_', ' ', $stage) }}</p>
                        </div>
                        @if(!$loop->last)
                            <div class="flex-1 h-0.5 mb-5 {{ $i < $currentIdx ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                        @endif
                    @endforeach
                </div>
            @endif

            {{-- Items --}}
            <div class="border-t pt-4">
                <h3 class="font-semibold text-gray-900 mb-3">Items Ordered</h3>
                <div class="space-y-2">
                    @foreach($order->items as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-700">{{ $item->product_name }} ({{ $item->quantity }} {{ $item->unit }})</span>
                            <span class="font-medium">GHS {{ number_format($item->subtotal, 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between font-bold text-gray-900 mt-3 pt-3 border-t">
                    <span>Total</span>
                    <span class="text-green-700">GHS {{ number_format($order->total, 2) }}</span>
                </div>
            </div>

            <div class="mt-4 flex items-center gap-2 text-sm text-gray-500">
                <span>Payment:</span>
                @if($order->payment_status === 'paid')
                    <span class="text-green-700 font-semibold">✅ Paid</span>
                @else
                    <span class="text-yellow-700 font-semibold">⏳ {{ ucfirst($order->payment_status) }}</span>
                @endif
            </div>
        </div>
    @endif

</div>
