<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Shop Orders</h1>
            <p class="mt-0.5 text-sm text-gray-500">Your hostel shop order history.</p>
        </div>
        <a href="{{ route('hostel_occupant.shop.index') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors w-full sm:w-auto justify-center sm:justify-start">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Shop
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
        @if($orders->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 mb-4">
                    <svg class="h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">No orders yet</p>
                <p class="mt-1 text-sm text-gray-500">Your shop orders will appear here.</p>
                <a href="{{ route('hostel_occupant.shop.index') }}"
                   class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                    Browse Shop
                </a>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($orders as $order)
                    <div x-data="{ open: false }" class="px-5 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-semibold text-gray-900">{{ $order->reference }}</p>
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                        Placed
                                    </span>
                                </div>
                                <p class="mt-0.5 text-xs text-gray-400">{{ $order->created_at?->format('M j, Y — g:i A') }}</p>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $order->lines->count() }} {{ Str::plural('item', $order->lines->count()) }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <p class="text-sm font-bold text-gray-900">GHS {{ number_format($order->total, 2) }}</p>
                                <button @click="open = !open"
                                        class="text-xs text-blue-600 hover:underline focus:outline-none">
                                    <span x-text="open ? 'Hide' : 'Details'"></span>
                                </button>
                            </div>
                        </div>

                        {{-- Expandable items --}}
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="mt-3 rounded-lg border border-gray-100 bg-gray-50 overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Item</th>
                                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Qty</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Price</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($order->lines as $line)
                                        <tr>
                                            <td class="px-4 py-2 text-gray-800">{{ $line->catalogItem?->name ?? '—' }}</td>
                                            <td class="px-4 py-2 text-center text-gray-600">{{ $line->quantity }}</td>
                                            <td class="px-4 py-2 text-right text-gray-600">GHS {{ number_format($line->unit_price, 2) }}</td>
                                            <td class="px-4 py-2 text-right font-medium text-gray-900">GHS {{ number_format($line->line_total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($orders->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $orders->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
