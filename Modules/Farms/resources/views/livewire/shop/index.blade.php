<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Hero Banner / Slider --}}
    @if($banners->isNotEmpty())
    <div class="relative rounded-2xl overflow-hidden mb-10"
         x-data="{ current: 0, total: {{ $banners->count() }} }"
         x-init="setInterval(() => { current = (current + 1) % total }, 5000)">

        @foreach($banners as $i => $banner)
        <div x-show="current === {{ $i }}"
             x-transition:enter="transition-opacity duration-700"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="relative min-h-[220px] flex items-center"
             style="{{ $banner->image_path ? 'background-image: url(' . \Illuminate\Support\Facades\Storage::url($banner->image_path) . '); background-size: cover; background-position: center;' : 'background: linear-gradient(135deg, #15803d, #166534);' }}">
            {{-- Overlay --}}
            <div class="absolute inset-0 rounded-2xl"
                 style="background-color: {{ $banner->overlay_color }}; opacity: {{ $banner->overlay_opacity / 100 }};"></div>
            {{-- Content --}}
            <div class="relative z-10 px-8 py-10 text-white">
                <h1 class="text-3xl font-bold mb-2">{{ $banner->title }}</h1>
                @if($banner->subtitle)
                    <p class="text-white/80 text-lg mb-4">{{ $banner->subtitle }}</p>
                @endif
                @if($banner->cta_text && $banner->cta_url)
                    <a href="{{ $banner->cta_url }}"
                       class="inline-block bg-white text-green-700 font-bold px-6 py-2.5 rounded-xl hover:bg-green-50 transition-colors">
                        {{ $banner->cta_text }}
                    </a>
                @endif
            </div>
        </div>
        @endforeach

        {{-- Dots --}}
        @if($banners->count() > 1)
        <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5 z-20">
            @foreach($banners as $i => $banner)
            <button x-on:click="current = {{ $i }}"
                    class="w-2 h-2 rounded-full transition-colors"
                    :class="current === {{ $i }} ? 'bg-white' : 'bg-white/40'"></button>
            @endforeach
        </div>
        @endif
    </div>
    @else
    <div class="bg-gradient-to-r from-green-700 to-green-500 rounded-2xl p-8 mb-10 text-white">
        <h1 class="text-3xl font-bold mb-2">🌾 Fresh from Our Farms</h1>
        <p class="text-green-100 text-lg">Order directly from Alpha Farms — harvested fresh, delivered to you.</p>
    </div>
    @endif

    {{-- Bundle Deals --}}
    @if($bundles->isNotEmpty())
    <div class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">🎁 Bundle Deals</h2>
            <span class="text-xs text-gray-400">Save more when you buy together</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($bundles as $bundle)
            <a href="{{ route('farm-shop.bundle.show', $bundle) }}"
               class="group bg-white rounded-xl shadow hover:shadow-lg transition-all overflow-hidden flex flex-col">
                @php $bImages = $bundle->images ?? []; @endphp
                <div class="h-36 relative bg-gradient-to-br from-emerald-100 to-green-200 flex items-center justify-center overflow-hidden">
                    @if(count($bImages))
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($bImages[0]) }}"
                             alt="{{ $bundle->name }}"
                             class="w-full h-full object-cover" />
                    @else
                        <span class="text-5xl">🎁</span>
                    @endif
                    <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ number_format($bundle->discount_percentage, 0) }}% OFF
                    </span>
                </div>
                <div class="p-4 flex flex-col flex-1">
                    <p class="text-xs text-green-600 font-semibold uppercase tracking-wider">Bundle Deal</p>
                    <h3 class="font-semibold text-gray-900 group-hover:text-green-700 transition-colors mt-0.5">{{ $bundle->name }}</h3>
                    @if($bundle->description)
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $bundle->description }}</p>
                    @endif
                    <div class="mt-auto pt-3 flex items-center justify-between">
                        <div>
                            <span class="text-lg font-bold text-green-700">GHS {{ number_format($bundle->bundlePrice(), 2) }}</span>
                            <span class="text-xs line-through text-gray-400 ml-1">GHS {{ number_format($bundle->retailTotal(), 2) }}</span>
                        </div>
                        <span class="text-xs text-gray-400">{{ $bundle->bundleItems->count() }} items</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-4 mb-8">
        <div class="flex-1">
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search produce..."
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
            />
        </div>
        <div>
            <select wire:model.live="farmFilter" class="rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                <option value="">All Farms</option>
                @foreach($farms as $farm)
                    <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Product Grid --}}
    @if($products->isEmpty())
        <div class="text-center py-20">
            <div class="text-6xl mb-4">🌱</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No produce available right now</h3>
            <p class="text-gray-500">Check back soon — our farms are always growing!</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                <a href="{{ route('farm-shop.show', $product) }}" class="group bg-white rounded-xl shadow hover:shadow-lg transition-all overflow-hidden flex flex-col">
                    {{-- Product image / placeholder --}}
                    @php $images = $product->images ?? []; $onSale = $product->isOnSale(); @endphp
                    <div class="h-40 relative bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center overflow-hidden">
                        @if(count($images))
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($images[0]) }}"
                                 alt="{{ $product->product_name }}"
                                 class="w-full h-full object-cover" />
                        @else
                            <span class="text-5xl">🥦</span>
                        @endif
                        @if($onSale)
                            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">SALE</span>
                        @endif
                    </div>
                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="font-semibold text-gray-900 group-hover:text-green-700 transition-colors">
                            {{ $product->product_name }}
                        </h3>
                        <p class="text-xs text-gray-400 mb-1">{{ $product->farm?->name }}</p>

                        @if($product->description)
                            <p class="text-sm text-gray-500 mb-3 line-clamp-2">{{ $product->description }}</p>
                        @endif

                        <div class="mt-auto flex items-center justify-between">
                            <div>
                                @if($onSale)
                                    <span class="text-lg font-bold text-red-600">GHS {{ number_format($product->sale_price, 2) }}</span>
                                    <span class="text-xs line-through text-gray-400 ml-1">GHS {{ number_format($product->unit_price, 2) }}</span>
                                @else
                                    <span class="text-lg font-bold text-green-700">GHS {{ number_format($product->unit_price, 2) }}</span>
                                @endif
                                <span class="text-xs text-gray-400">/ {{ $product->unit }}</span>
                            </div>
                            @if($product->status === 'low_stock')
                                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-medium">Low Stock</span>
                            @elseif($product->current_stock <= 0 || $product->status === 'depleted')
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-medium">Out of Stock</span>
                            @endif
                        </div>

                        <button class="mt-3 w-full bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 rounded-lg transition-colors">
                            {{ ($product->current_stock <= 0 || $product->status === 'depleted') ? 'Notify Me' : 'View Product' }}
                        </button>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @endif

</div>
