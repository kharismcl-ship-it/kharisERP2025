<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Blog & Recipes</h1>
        <p class="text-gray-500">Farm tips, nutrition guides, and seasonal recipes straight from the farm.</p>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-8">
        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            placeholder="Search posts..."
            class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
        />
        <select wire:model.live="category"
                class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">All Posts</option>
            <option value="blog">Blog</option>
            <option value="recipe">Recipes</option>
        </select>
    </div>

    @if($posts->isEmpty())
        <div class="text-center py-20 text-gray-400">
            <p class="text-4xl mb-3">📝</p>
            <p class="text-lg">No posts found.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            @foreach($posts as $post)
            <a href="{{ route('farm-shop.blog.show', $post->slug) }}"
               class="group bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition-shadow">

                {{-- Cover image --}}
                @if($post->cover_image_path)
                    <div class="h-48 overflow-hidden">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($post->cover_image_path) }}"
                             alt="{{ $post->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                    </div>
                @else
                    <div class="h-48 bg-gradient-to-br from-green-100 to-emerald-200 flex items-center justify-center">
                        <span class="text-6xl">{{ $post->category === 'recipe' ? '🍲' : '🌿' }}</span>
                    </div>
                @endif

                <div class="p-5">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $post->category === 'recipe' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                            {{ $post->category === 'recipe' ? '🍲 Recipe' : '📰 Blog' }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $post->reading_time_minutes }} min read</span>
                    </div>

                    <h2 class="text-base font-bold text-gray-900 mb-1 group-hover:text-green-700 transition-colors line-clamp-2">
                        {{ $post->title }}
                    </h2>

                    @if($post->excerpt)
                        <p class="text-sm text-gray-500 line-clamp-2 mb-3">{{ $post->excerpt }}</p>
                    @endif

                    <div class="flex items-center justify-between text-xs text-gray-400 mt-auto">
                        <span>{{ $post->published_at?->format('M j, Y') ?? $post->created_at->format('M j, Y') }}</span>
                        @if($post->tags)
                            <span>{{ implode(' · ', array_slice($post->tags, 0, 2)) }}</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{ $posts->links() }}
    @endif

</div>
