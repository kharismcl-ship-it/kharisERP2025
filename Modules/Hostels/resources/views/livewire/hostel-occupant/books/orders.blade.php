<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Book Orders</h1>
            <p class="mt-0.5 text-sm text-gray-500">Your book purchase history.</p>
        </div>
        <a href="{{ route('hostel_occupant.books.index') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors w-full sm:w-auto justify-center sm:justify-start">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Book Store
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
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">No orders yet</p>
                <p class="mt-1 text-sm text-gray-500">Your book orders will appear here once you make a purchase.</p>
                <a href="{{ route('hostel_occupant.books.index') }}"
                   class="mt-4 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                    Browse Books
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
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        @if($order->status === 'paid') bg-green-100 text-green-700
                                        @elseif($order->status === 'processing') bg-blue-100 text-blue-700
                                        @elseif($order->status === 'delivered') bg-purple-100 text-purple-700
                                        @elseif($order->status === 'cancelled') bg-gray-100 text-gray-500
                                        @else bg-yellow-100 text-yellow-700
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <p class="mt-0.5 text-xs text-gray-400">{{ $order->created_at?->format('M j, Y — g:i A') }}</p>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $order->items->count() }} {{ Str::plural('book', $order->items->count()) }}
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
                             class="mt-3 rounded-lg border border-gray-100 bg-gray-50 overflow-hidden divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <div class="px-4 py-3 flex items-center justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $item->book?->title ?? 'Book' }}
                                        </p>
                                        @if($item->book?->author)
                                            <p class="text-xs text-gray-400 mt-0.5">by {{ $item->book->author }}</p>
                                        @endif
                                        <div class="mt-1 flex items-center gap-2">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                {{ ($item->book?->book_type ?? 'physical') === 'digital' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
                                                {{ ucfirst($item->book?->book_type ?? 'physical') }}
                                            </span>
                                            <span class="text-xs text-gray-400">Qty: {{ $item->quantity }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-sm font-semibold text-gray-900">GHS {{ number_format($item->subtotal, 2) }}</p>
                                        <p class="text-xs text-gray-400">@ GHS {{ number_format($item->unit_price, 2) }}</p>

                                        {{-- Download link for paid digital books --}}
                                        @if($order->status === 'paid' && $item->book?->book_type === 'digital' && $item->book?->digital_file)
                                            <a href="{{ Storage::url($item->book->digital_file) }}"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               class="mt-1 inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:underline">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                Download
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            @if($order->notes)
                                <div class="px-4 py-3 bg-white">
                                    <p class="text-xs text-gray-500">
                                        <span class="font-medium text-gray-600">Notes:</span> {{ $order->notes }}
                                    </p>
                                </div>
                            @endif
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
