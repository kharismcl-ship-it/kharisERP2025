<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $movie->title }}</h1>
            <div class="mt-1 flex items-center gap-2 flex-wrap">
                @if($movie->genre)
                    <span class="inline-flex items-center rounded-full bg-purple-50 px-2.5 py-0.5 text-xs font-medium text-purple-700">
                        {{ $movie->genre }}
                    </span>
                @endif
                @if($movie->duration_minutes)
                    <span class="text-sm text-gray-400">
                        {{ floor($movie->duration_minutes / 60) > 0 ? floor($movie->duration_minutes / 60) . 'h ' : '' }}{{ $movie->duration_minutes % 60 }}m
                    </span>
                @endif
                @if(! $movie->requires_payment)
                    <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700">Free</span>
                @endif
            </div>
        </div>
        <a href="{{ route('hostel_occupant.movies.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors w-full sm:w-auto justify-center sm:justify-start">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Movies
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if($hasAccess || ! $movie->requires_payment)

        {{-- ── Video player ───────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
            @if($movie->video_url)
                <div class="aspect-video w-full bg-black">
                    @php
                        // Detect YouTube or embed-compatible URLs
                        $videoUrl  = $movie->video_url;
                        $isYoutube = str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be');
                        if ($isYoutube) {
                            // Convert to embed URL if needed
                            preg_match('/(?:v=|youtu\.be\/)([A-Za-z0-9_\-]+)/', $videoUrl, $m);
                            $videoId  = $m[1] ?? null;
                            $embedUrl = $videoId ? 'https://www.youtube.com/embed/' . $videoId : $videoUrl;
                        }
                    @endphp
                    @if($isYoutube && isset($embedUrl))
                        <iframe src="{{ $embedUrl }}"
                                class="h-full w-full"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    @else
                        <video controls class="h-full w-full" preload="metadata">
                            <source src="{{ $videoUrl }}">
                            Your browser does not support the video tag.
                        </video>
                    @endif
                </div>
            @elseif($movie->video_file)
                <div class="aspect-video w-full bg-black">
                    <video controls class="h-full w-full" preload="metadata">
                        <source src="{{ Storage::url($movie->video_file) }}">
                        Your browser does not support the video tag.
                    </video>
                </div>
            @else
                <div class="aspect-video bg-gray-100 flex flex-col items-center justify-center gap-3">
                    <svg class="h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                              d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4"/>
                    </svg>
                    <p class="text-sm text-gray-500">Video file not yet uploaded.</p>
                </div>
            @endif

            {{-- Movie description --}}
            @if($movie->description)
                <div class="p-5 border-t border-gray-100">
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $movie->description }}</p>
                </div>
            @endif
        </div>

    @else

        {{-- ── Locked state — payment required ──────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">

            {{-- Blurred thumbnail or placeholder --}}
            <div class="relative aspect-video w-full bg-gray-900 overflow-hidden">
                @if($movie->thumbnail)
                    <img src="{{ $movie->thumbnail }}" alt="{{ $movie->title }}"
                         class="h-full w-full object-cover opacity-30 blur-sm scale-110">
                @else
                    <div class="h-full w-full flex items-center justify-center">
                        <svg class="h-20 w-20 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                  d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4"/>
                        </svg>
                    </div>
                @endif

                {{-- Lock overlay --}}
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-4 px-6 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-semibold text-lg">This movie requires payment</p>
                        <p class="text-white/70 text-sm mt-1">Purchase access for 48 hours of viewing.</p>
                    </div>
                    <button wire:click="initiatePayment"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-70 cursor-not-allowed"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700 transition-colors shadow-lg">
                        <svg class="h-4 w-4" wire:loading.remove wire:target="initiatePayment" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <span wire:loading.remove wire:target="initiatePayment">
                            Pay GHS {{ number_format($movie->price, 2) }} to Watch
                        </span>
                        <span wire:loading wire:target="initiatePayment">Processing...</span>
                    </button>
                </div>
            </div>

            @if($movie->description)
                <div class="p-5 border-t border-gray-100">
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $movie->description }}</p>
                </div>
            @endif
        </div>

    @endif

</div>
