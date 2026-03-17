<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <a href="{{ route('farm-shop.index') }}" class="inline-flex items-center gap-1.5 text-green-700 hover:text-green-900 text-sm font-medium mb-6 block">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Shop
    </a>

    @if(session('error'))
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        {{-- Bundle header image --}}
        @php $images = $bundle->images ?? []; @endphp
        @if(count($images))
            <div class="h-56 overflow-hidden">
                <img src="{{ \Illuminate\Support\Facades\Storage::url($images[0]) }}" alt="{{ $bundle->name }}" class="w-full h-full object-cover" />
            </div>
        @else
            <div class="h-40 bg-gradient-to-br from-green-100 to-emerald-200 flex items-center justify-center">
                <span class="text-7xl">🎁</span>
            </div>
        @endif

        <div class="p-8">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <span class="text-xs text-green-600 font-semibold uppercase tracking-wider">Bundle Deal</span>
                    <h1 class="text-2xl font-bold text-gray-900 mt-0.5">{{ $bundle->name }}</h1>
                    @if($bundle->description)
                        <p class="text-gray-500 mt-2">{{ $bundle->description }}</p>
                    @endif
                </div>
                <div class="text-right ml-6">
                    <span class="inline-block bg-red-100 text-red-700 text-lg font-bold px-3 py-1.5 rounded-xl">
                        {{ number_format($bundle->discount_percentage, 0) }}% OFF
                    </span>
                </div>
            </div>

            {{-- Bundle contents --}}
            <div class="border border-gray-100 rounded-xl overflow-hidden mb-6">
                <div class="bg-gray-50 px-4 py-2 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">What's Included</p>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($bundle->bundleItems as $item)
                        @php
                            $product      = $item->product;
                            $basePrice    = $product?->getEffectiveBasePrice() ?? 0;
                            $bundlePrice  = round($basePrice * (1 - (float) $bundle->discount_percentage / 100), 2);
                            $saving       = round(($basePrice - $bundlePrice) * (float) $item->quantity, 2);
                        @endphp
                        <div class="flex items-center gap-4 px-4 py-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                @php $pImages = $product?->images ?? []; @endphp
                                @if(count($pImages))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($pImages[0]) }}" class="w-10 h-10 object-cover rounded-lg" />
                                @else
                                    <span>🌿</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $product?->product_name ?? '—' }}</p>
                                <p class="text-xs text-gray-400">{{ number_format($item->quantity, 1) }} {{ $product?->unit }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-green-700">GHS {{ number_format($bundlePrice, 2) }}<span class="text-xs text-gray-400">/{{ $product?->unit }}</span></p>
                                <p class="text-xs line-through text-gray-400">GHS {{ number_format($basePrice, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Pricing summary --}}
            <div class="bg-green-50 rounded-xl p-5 mb-6">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Regular price</span>
                    <span class="line-through">GHS {{ number_format($bundle->retailTotal(), 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-green-700 mb-2">
                    <span>Bundle discount ({{ number_format($bundle->discount_percentage, 0) }}%)</span>
                    <span>− GHS {{ number_format($bundle->retailTotal() - $bundle->bundlePrice(), 2) }}</span>
                </div>
                <div class="flex justify-between text-xl font-bold text-gray-900 border-t border-green-200 pt-2">
                    <span>Bundle Price</span>
                    <span class="text-green-700">GHS {{ number_format($bundle->bundlePrice(), 2) }}</span>
                </div>
            </div>

            {{-- Add to cart --}}
            <button
                wire:click="addToCart"
                wire:loading.attr="disabled"
                class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-bold py-4 rounded-xl transition-colors flex items-center justify-center gap-2 text-lg">
                <span wire:loading.remove wire:target="addToCart">🛒 Add Bundle to Cart</span>
                <span wire:loading wire:target="addToCart">Adding...</span>
            </button>
        </div>
    </div>

</div>
