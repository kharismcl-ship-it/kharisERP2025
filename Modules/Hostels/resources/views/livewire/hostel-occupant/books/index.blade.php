<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Book Store</h1>
            <p class="mt-0.5 text-sm text-gray-500">Browse and purchase books.</p>
        </div>
        <a href="{{ route('hostel_occupant.books.orders') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors w-full sm:w-auto justify-center sm:justify-start">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            My Orders
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Search & filter ─────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Search by title or author..."
                   class="block w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>

        {{-- Type filter tabs --}}
        <div class="flex gap-2 shrink-0">
            <button wire:click="$set('typeFilter', '')"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors
                        {{ $typeFilter === '' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                All
            </button>
            <button wire:click="$set('typeFilter', 'physical')"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors
                        {{ $typeFilter === 'physical' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                Physical
            </button>
            <button wire:click="$set('typeFilter', 'digital')"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors
                        {{ $typeFilter === 'digital' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                Digital
            </button>
        </div>
    </div>

    {{-- ── Books grid ───────────────────────────────────────────────────── --}}
    @if($books->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
            <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 mb-4">
                    <svg class="h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">No books found</p>
                <p class="mt-1 text-sm text-gray-500">
                    @if($search)
                        No books match "{{ $search }}".
                    @else
                        No books are available for your hostel yet.
                    @endif
                </p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($books as $book)
                <div class="rounded-xl border border-gray-200 bg-white overflow-hidden flex flex-col">

                    {{-- Cover image --}}
                    @if($book->cover_image)
                        <div class="h-48 overflow-hidden bg-gray-100">
                            <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}"
                                 class="h-full w-full object-cover">
                        </div>
                    @else
                        <div class="h-48 bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center">
                            <svg class="h-16 w-16 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    @endif

                    <div class="p-4 flex flex-col gap-3 flex-1">
                        {{-- Title, author, badges --}}
                        <div>
                            <p class="text-sm font-semibold text-gray-900 leading-tight">{{ $book->title }}</p>
                            <p class="mt-0.5 text-xs text-gray-500">by {{ $book->author }}</p>
                            <div class="mt-1.5 flex items-center gap-2 flex-wrap">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                    {{ $book->book_type === 'digital' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $book->book_type === 'digital' ? 'Digital' : 'Physical' }}
                                </span>
                                @if($book->book_type === 'physical')
                                    @if($book->stock_qty > 5)
                                        <span class="text-xs text-green-600">In stock ({{ $book->stock_qty }})</span>
                                    @elseif($book->stock_qty > 0)
                                        <span class="text-xs text-orange-600">Low stock ({{ $book->stock_qty }} left)</span>
                                    @else
                                        <span class="text-xs text-red-600">Out of stock</span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        {{-- Price & cart controls --}}
                        <div class="mt-auto flex items-center justify-between gap-3 pt-2">
                            <span class="text-base font-bold text-gray-900">GHS {{ number_format($book->price, 2) }}</span>

                            @php $qty = $cart[$book->id] ?? 0; @endphp
                            @if($book->book_type === 'physical' && $book->stock_qty <= 0)
                                <span class="text-xs text-gray-400 italic">Unavailable</span>
                            @elseif($qty > 0)
                                <div class="flex items-center gap-2">
                                    <button wire:click="removeFromCart({{ $book->id }})"
                                            class="flex h-8 w-8 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition-colors">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
                                        </svg>
                                    </button>
                                    <span class="w-5 text-center text-sm font-semibold text-gray-900">{{ $qty }}</span>
                                    <button wire:click="addToCart({{ $book->id }})"
                                            @if($book->book_type === 'physical') @disabled($qty >= $book->stock_qty) @endif
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <button wire:click="addToCart({{ $book->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add to Cart
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($books->hasPages())
            <div class="flex justify-center">
                {{ $books->links() }}
            </div>
        @endif
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
                <button wire:click="checkout"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="inline-flex items-center gap-2 rounded-lg bg-white px-5 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50 transition-colors">
                    <span wire:loading.remove wire:target="checkout">Checkout</span>
                    <span wire:loading wire:target="checkout">Redirecting...</span>
                </button>
            </div>
        </div>
    @endif

</div>
