<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Hostel Shop</h1>
            <p class="mt-0.5 text-sm text-gray-500">Browse and order items for delivery to your room.</p>
        </div>
        @if($this->cartCount > 0)
            <a href="{{ route('hostel_occupant.shop.orders') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors w-full sm:w-auto justify-center sm:justify-start">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                My Orders
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(! $terminal)
        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
            <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 mb-4">
                    <svg class="h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">No shop configured</p>
                <p class="mt-1 text-sm text-gray-500">No shop has been configured for your hostel yet.</p>
            </div>
        </div>
    @else

        {{-- ── Search ───────────────────────────────────────────────────────── --}}
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Search items..."
                   class="block w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>

        {{-- ── Items grid ───────────────────────────────────────────────────── --}}
        @if($items->isEmpty())
            <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
                <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                    <p class="text-sm font-medium text-gray-700">No items found</p>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($search)
                            No items match your search "{{ $search }}".
                        @else
                            No items are available in the shop right now.
                        @endif
                    </p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($items as $item)
                    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden flex flex-col">
                        <div class="p-4 flex-1 flex flex-col gap-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ $item->name }}</p>
                                    @if($item->description)
                                        <p class="mt-0.5 text-xs text-gray-500 line-clamp-2">{{ $item->description }}</p>
                                    @endif
                                </div>
                                @if($item->source_type)
                                    <span class="shrink-0 inline-flex items-center rounded-full bg-purple-50 px-2 py-0.5 text-xs font-medium text-purple-700">
                                        {{ ucfirst($item->source_type) }}
                                    </span>
                                @endif
                            </div>

                            <div class="mt-auto flex items-center justify-between gap-3 pt-2">
                                <span class="text-base font-bold text-gray-900">GHS {{ number_format($item->base_price, 2) }}</span>

                                @php $qty = $cart[$item->id]['qty'] ?? 0; @endphp
                                @if($qty > 0)
                                    <div class="flex items-center gap-2">
                                        <button wire:click="removeFromCart({{ $item->id }})"
                                                class="flex h-8 w-8 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <span class="w-5 text-center text-sm font-semibold text-gray-900">{{ $qty }}</span>
                                        <button wire:click="addToCart({{ $item->id }})"
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <button wire:click="addToCart({{ $item->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ── Sticky cart bar ─────────────────────────────────────────────── --}}
        @if($this->cartCount > 0)
            <div class="sticky bottom-4 z-10 mx-auto max-w-xl">
                <div class="rounded-xl border border-blue-200 bg-blue-600 px-5 py-3.5 shadow-lg flex items-center justify-between gap-4">
                    <div class="text-white">
                        <span class="text-sm font-medium">{{ $this->cartCount }} {{ Str::plural('item', $this->cartCount) }}</span>
                        <span class="mx-2 text-blue-300">·</span>
                        <span class="text-sm font-bold">GHS {{ number_format($this->cartTotal, 2) }}</span>
                    </div>
                    <button wire:click="storeCartAndRedirect"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-70 cursor-not-allowed"
                            class="inline-flex items-center gap-2 rounded-lg bg-white px-5 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50 transition-colors">
                        <span wire:loading.remove wire:target="storeCartAndRedirect">Checkout</span>
                        <span wire:loading wire:target="storeCartAndRedirect">Redirecting...</span>
                    </button>
                </div>
            </div>
        @endif

    @endif

</div>
