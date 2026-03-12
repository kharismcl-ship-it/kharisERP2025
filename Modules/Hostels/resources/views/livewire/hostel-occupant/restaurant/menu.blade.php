<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Restaurant</h1>
            <p class="mt-0.5 text-sm text-gray-500">Order food for room delivery.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(! $restaurant)
        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
            <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 mb-4">
                    <svg class="h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">No restaurant linked</p>
                <p class="mt-1 text-sm text-gray-500">No restaurant is linked to your hostel yet.</p>
            </div>
        </div>
    @else

        {{-- ── Category filter tabs ────────────────────────────────────────── --}}
        @if($categories->isNotEmpty())
            <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1">
                <button wire:click="$set('activeCategory', 'all')"
                        class="shrink-0 rounded-full px-4 py-1.5 text-sm font-medium transition-colors
                            {{ $activeCategory === 'all' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                    All
                </button>
                @foreach($categories as $cat)
                    <button wire:click="$set('activeCategory', '{{ $cat }}')"
                            class="shrink-0 rounded-full px-4 py-1.5 text-sm font-medium transition-colors
                                {{ $activeCategory === $cat ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                        {{ ucfirst($cat) }}
                    </button>
                @endforeach
            </div>
        @endif

        {{-- ── Menu grid ───────────────────────────────────────────────────── --}}
        @if($menuItems->isEmpty())
            <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
                <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                    <p class="text-sm font-medium text-gray-700">No menu items available</p>
                    <p class="mt-1 text-sm text-gray-500">No items are available in this category right now.</p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($menuItems as $item)
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
                                    <span class="shrink-0 inline-flex items-center rounded-full bg-orange-50 px-2 py-0.5 text-xs font-medium text-orange-700">
                                        {{ ucfirst($item->source_type) }}
                                    </span>
                                @endif
                            </div>

                            <div class="mt-auto flex items-center justify-between gap-3 pt-2">
                                <span class="text-base font-bold text-gray-900">GHS {{ number_format($item->base_price, 2) }}</span>

                                @php $qty = $cart[$item->id] ?? 0; @endphp
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
        @if($cartCount > 0)
            <div class="sticky bottom-4 z-10 mx-auto max-w-xl">
                <div class="rounded-xl border border-blue-200 bg-blue-600 px-5 py-3.5 shadow-lg flex items-center justify-between gap-4">
                    <div class="text-white">
                        <span class="text-sm font-medium">{{ $cartCount }} {{ Str::plural('item', $cartCount) }}</span>
                        <span class="mx-2 text-blue-300">·</span>
                        <span class="text-sm font-bold">GHS {{ number_format($cartTotal, 2) }}</span>
                    </div>
                    <button wire:click="placeOrder"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-70 cursor-not-allowed"
                            class="inline-flex items-center gap-2 rounded-lg bg-white px-5 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50 transition-colors">
                        <span wire:loading.remove wire:target="placeOrder">Place Order</span>
                        <span wire:loading wire:target="placeOrder">Placing...</span>
                    </button>
                </div>
            </div>
        @endif

    @endif

</div>
