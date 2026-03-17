<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <a href="{{ route('farm-shop.index') }}"
       class="inline-flex items-center gap-1.5 text-green-700 hover:text-green-900 text-sm font-medium mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Shop
    </a>

    {{-- Hero --}}
    @if($farm->cover_image)
        <div class="h-64 sm:h-80 rounded-2xl overflow-hidden mb-8">
            <img src="{{ \Illuminate\Support\Facades\Storage::url($farm->cover_image) }}"
                 alt="{{ $farm->name }}"
                 class="w-full h-full object-cover" />
        </div>
    @else
        <div class="h-48 rounded-2xl bg-gradient-to-br from-green-200 to-emerald-400 flex items-center justify-center mb-8">
            <span class="text-8xl">🌾</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm p-8 mb-8">

        {{-- Farm Header --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1">{{ $farm->name }}</h1>
                <div class="flex flex-wrap gap-3 text-sm text-gray-500">
                    @if($farm->location)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $farm->location }}
                        </span>
                    @endif
                    @if($farm->established_year)
                        <span>Est. {{ $farm->established_year }}</span>
                    @endif
                    @if($farm->type)
                        <span class="capitalize">{{ str_replace('_', ' ', $farm->type) }} farm</span>
                    @endif
                </div>
            </div>
            @if($farm->owner_phone)
                <a href="tel:{{ $farm->owner_phone }}"
                   class="inline-flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 text-sm font-medium px-4 py-2 rounded-xl hover:bg-green-100 transition-colors self-start">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 7V5z"/>
                    </svg>
                    Call {{ $farm->owner_name ?? 'Farmer' }}
                </a>
            @endif
        </div>

        {{-- About --}}
        @if($farm->about)
            <div class="prose prose-green max-w-none text-gray-700 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-2">About the Farm</h2>
                <p class="leading-relaxed">{{ $farm->about }}</p>
            </div>
        @elseif($farm->description)
            <div class="text-gray-700 leading-relaxed mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-2">About the Farm</h2>
                <p>{{ $farm->description }}</p>
            </div>
        @endif

        {{-- Video --}}
        @if($farm->video_url)
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-3">Farm Tour</h2>
                <div class="relative rounded-xl overflow-hidden bg-black" style="padding-top:56.25%">
                    <iframe
                        src="{{ $farm->video_url }}"
                        class="absolute inset-0 w-full h-full"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        @endif

        {{-- Gallery --}}
        @if($farm->gallery_images && count($farm->gallery_images) > 0)
            <div>
                <h2 class="text-lg font-bold text-gray-800 mb-3">Farm Gallery</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($farm->gallery_images as $img)
                        <div class="rounded-xl overflow-hidden h-40">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($img) }}"
                                 alt="{{ $farm->name }}"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300 cursor-pointer" />
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Available Produce --}}
    @if($produce->isNotEmpty())
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Available Produce from {{ $farm->name }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($produce as $item)
                    <a href="{{ route('farm-shop.show', $item) }}"
                       class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow p-4 flex items-center gap-3">
                        @php $img = $item->images[0] ?? null; @endphp
                        @if($img)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($img) }}"
                                 class="w-14 h-14 rounded-lg object-cover flex-shrink-0" />
                        @else
                            <div class="w-14 h-14 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0 text-2xl">🌿</div>
                        @endif
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $item->product_name }}</p>
                            <p class="text-green-700 font-bold">GHS {{ number_format($item->unit_price, 2) }}</p>
                            <p class="text-xs text-gray-400">per {{ $item->unit }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
