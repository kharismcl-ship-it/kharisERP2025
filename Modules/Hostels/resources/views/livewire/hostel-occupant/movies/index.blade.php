<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Movie Centre</h1>
            <p class="mt-0.5 text-sm text-gray-500">Watch movies and request new titles.</p>
        </div>
        <a href="{{ route('hostel_occupant.movies.request') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors w-full sm:w-auto justify-center sm:justify-start">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Request a Movie
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Search ───────────────────────────────────────────────────────── --}}
    <div class="relative">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input type="text"
               wire:model.live.debounce.300ms="search"
               placeholder="Search movies..."
               class="block w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>

    @if($movies->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
            <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 mb-4">
                    <svg class="h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">No movies available</p>
                <p class="mt-1 text-sm text-gray-500">
                    @if($search)
                        No movies match "{{ $search }}".
                    @else
                        No movies have been added for your hostel yet.
                    @endif
                </p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($movies as $movie)
                @php $purchased = in_array($movie->id, $purchasedIds); @endphp
                <div class="rounded-xl border border-gray-200 bg-white overflow-hidden flex flex-col">

                    {{-- Thumbnail --}}
                    @if($movie->thumbnail)
                        <div class="aspect-video overflow-hidden bg-gray-100">
                            <img src="{{ $movie->thumbnail }}" alt="{{ $movie->title }}"
                                 class="h-full w-full object-cover">
                        </div>
                    @else
                        <div class="aspect-video bg-gray-100 flex items-center justify-center">
                            <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                      d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                            </svg>
                        </div>
                    @endif

                    <div class="p-4 flex flex-col gap-3 flex-1">
                        {{-- Title & meta --}}
                        <div>
                            <p class="text-sm font-semibold text-gray-900 leading-tight">{{ $movie->title }}</p>
                            <div class="mt-1.5 flex items-center gap-2 flex-wrap">
                                @if($movie->genre)
                                    <span class="inline-flex items-center rounded-full bg-purple-50 px-2 py-0.5 text-xs font-medium text-purple-700">
                                        {{ $movie->genre }}
                                    </span>
                                @endif
                                @if($movie->duration_minutes)
                                    <span class="text-xs text-gray-400">
                                        {{ floor($movie->duration_minutes / 60) > 0 ? floor($movie->duration_minutes / 60) . 'h ' : '' }}{{ $movie->duration_minutes % 60 }}m
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Price --}}
                        <div class="mt-auto">
                            @if(! $movie->requires_payment)
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700">Free</span>
                            @elseif($purchased)
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">Purchased</span>
                            @else
                                <p class="text-sm font-bold text-gray-900">GHS {{ number_format($movie->price, 2) }}</p>
                            @endif
                        </div>

                        {{-- Action button --}}
                        @if($purchased || ! $movie->requires_payment)
                            <a href="{{ route('hostel_occupant.movies.watch', $movie) }}"
                               class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Watch Now
                            </a>
                        @else
                            <a href="{{ route('hostel_occupant.movies.watch', $movie) }}"
                               class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-blue-600 bg-white px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-50 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Buy &amp; Watch — GHS {{ number_format($movie->price, 2) }}
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if($movies->hasPages())
            <div class="flex justify-center">
                {{ $movies->links() }}
            </div>
        @endif
    @endif

</div>
