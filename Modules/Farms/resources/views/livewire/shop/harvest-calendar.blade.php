<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <a href="{{ route('farm-shop.index') }}" class="inline-flex items-center gap-1.5 text-green-700 hover:text-green-900 text-sm font-medium mb-4 block">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Shop
        </a>
        <h1 class="text-3xl font-bold text-gray-900">🗓️ Harvest Calendar</h1>
        <p class="text-gray-500 mt-2">Upcoming fresh produce from our farms — order ahead or sign up to be notified.</p>
    </div>

    @if($upcomingHarvests->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-16 text-center">
            <div class="text-6xl mb-4">🌱</div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No upcoming harvests listed yet</h3>
            <p class="text-gray-500">Check back soon — our farms are always growing!</p>
        </div>
    @else
        @foreach($upcomingHarvests as $yearMonth => $products)
            @php
                $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $yearMonth)->format('F Y');
            @endphp

            <div class="mb-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-0.5 flex-1 bg-green-100 rounded"></div>
                    <h2 class="text-lg font-bold text-green-800 px-4 py-1.5 bg-green-50 rounded-full">{{ $monthLabel }}</h2>
                    <div class="h-0.5 flex-1 bg-green-100 rounded"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($products as $product)
                        @php
                            $inStock = $product->current_stock > 0 && $product->status !== 'depleted';
                            $onSale  = $product->isOnSale();
                        @endphp
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                            {{-- Image --}}
                            @php $images = $product->images ?? []; @endphp
                            <div class="h-36 relative bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center overflow-hidden">
                                @if(count($images))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($images[0]) }}"
                                         alt="{{ $product->product_name }}"
                                         class="w-full h-full object-cover" />
                                @else
                                    <span class="text-5xl">🌿</span>
                                @endif
                                @if($onSale)
                                    <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">SALE</span>
                                @endif
                                @if(! $inStock)
                                    <div class="absolute inset-0 bg-gray-900/40 flex items-center justify-center">
                                        <span class="text-white text-sm font-semibold bg-gray-700/70 px-3 py-1 rounded-full">Coming Soon</span>
                                    </div>
                                @endif
                            </div>

                            <div class="p-4">
                                <p class="text-xs text-green-600 font-semibold uppercase tracking-wider mb-0.5">{{ $product->farm?->name }}</p>
                                <h3 class="font-bold text-gray-900 mb-1">{{ $product->product_name }}</h3>

                                {{-- Harvest date --}}
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-3">
                                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Harvest: {{ $product->harvest_date->format('M j, Y') }}
                                    @if($product->harvest_date->isFuture())
                                        <span class="text-amber-600 font-medium">(in {{ $product->harvest_date->diffForHumans() }})</span>
                                    @endif
                                </div>

                                {{-- Price --}}
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        @if($onSale)
                                            <span class="font-bold text-red-600">GHS {{ number_format($product->sale_price, 2) }}</span>
                                            <span class="text-xs line-through text-gray-400 ml-1">GHS {{ number_format($product->unit_price, 2) }}</span>
                                        @else
                                            <span class="font-bold text-green-700">GHS {{ number_format($product->unit_price, 2) }}</span>
                                        @endif
                                        <span class="text-xs text-gray-400">/ {{ $product->unit }}</span>
                                    </div>
                                    @if($inStock)
                                        <span class="text-xs text-gray-400">{{ number_format($product->current_stock, 1) }} {{ $product->unit }} avail.</span>
                                    @endif
                                </div>

                                {{-- CTA --}}
                                @if($inStock)
                                    <a href="{{ route('farm-shop.show', $product) }}"
                                       class="block w-full text-center bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 rounded-lg transition-colors">
                                        Order Now
                                    </a>
                                @else
                                    <a href="{{ route('farm-shop.show', $product) }}"
                                       class="block w-full text-center border border-green-300 text-green-700 hover:bg-green-50 text-sm font-semibold py-2 rounded-lg transition-colors">
                                        🔔 Notify Me
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif

</div>
