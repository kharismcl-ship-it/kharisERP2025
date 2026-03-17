<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <a href="{{ route('farm-shop.blog.index') }}"
       class="inline-flex items-center gap-1.5 text-green-700 hover:text-green-900 text-sm font-medium mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Blog
    </a>

    <article class="bg-white rounded-2xl shadow-sm overflow-hidden">

        {{-- Cover --}}
        @if($post->cover_image_path)
            <div class="h-64 overflow-hidden">
                <img src="{{ \Illuminate\Support\Facades\Storage::url($post->cover_image_path) }}"
                     alt="{{ $post->title }}"
                     class="w-full h-full object-cover" />
            </div>
        @else
            <div class="h-48 bg-gradient-to-br from-green-100 to-emerald-200 flex items-center justify-center">
                <span class="text-8xl">{{ $post->category === 'recipe' ? '🍲' : '🌿' }}</span>
            </div>
        @endif

        <div class="p-8">
            {{-- Meta --}}
            <div class="flex flex-wrap items-center gap-3 mb-4 text-xs text-gray-500">
                <span class="font-semibold px-2.5 py-0.5 rounded-full {{ $post->category === 'recipe' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                    {{ $post->category === 'recipe' ? '🍲 Recipe' : '📰 Blog' }}
                </span>
                <span>{{ $post->published_at?->format('F j, Y') ?? $post->created_at->format('F j, Y') }}</span>
                <span>· {{ $post->reading_time_minutes }} min read</span>
                @if($post->tags)
                    @foreach($post->tags as $tag)
                        <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $tag }}</span>
                    @endforeach
                @endif
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>

            @if($post->excerpt)
                <p class="text-lg text-gray-600 mb-6 leading-relaxed border-l-4 border-green-400 pl-4">{{ $post->excerpt }}</p>
            @endif

            {{-- Ingredients (recipe only) --}}
            @if($post->category === 'recipe' && $post->ingredients)
                <div class="mb-6 bg-amber-50 rounded-xl p-4">
                    <h2 class="text-sm font-bold text-amber-800 uppercase tracking-wider mb-3">Ingredients</h2>
                    <ul class="space-y-1">
                        @foreach($post->ingredients as $ingredient)
                            <li class="flex items-start gap-2 text-sm text-gray-700">
                                <span class="text-amber-500 mt-0.5">•</span> {{ $ingredient }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Content --}}
            <div class="prose prose-green max-w-none text-gray-800 leading-relaxed">
                {!! $post->content !!}
            </div>
        </div>
    </article>

</div>
