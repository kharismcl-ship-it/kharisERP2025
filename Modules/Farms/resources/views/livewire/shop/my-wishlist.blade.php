<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Wishlist</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $wishlistItems->count() }} saved {{ Str::plural('item', $wishlistItems->count()) }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('farm-shop.my-orders') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">My Orders</a>
            <a href="{{ route('farm-shop.index') }}" class="text-sm font-medium text-green-700 hover:text-green-900">← Continue Shopping</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if($wishlistItems->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-16 text-center">
            <div class="text-6xl mb-4">🤍</div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Your wishlist is empty</h3>
            <p class="text-gray-500 mb-6">Save products you love by clicking the ♡ button on any product page.</p>
            <a href="{{ route('farm-shop.index') }}"
                class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-colors">
                Browse Products
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($wishlistItems as $item)
                @php $product = $item->product; @endphp
                @if($product)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col">
                    {{-- Product image --}}
                    @php $images = $product->images ?? []; @endphp
                    @if(count($images))
                        <div class="h-40 overflow-hidden bg-gray-100">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($images[0]) }}"
                                 alt="{{ $product->product_name }}"
                                 class="w-full h-full object-cover" />
                        </div>
                    @else
                        <div class="h-40 bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                            <span class="text-5xl">🌿</span>
                        </div>
                    @endif

                    <div class="p-4 flex flex-col flex-1">
                        <p class="text-xs text-green-600 font-medium mb-0.5">{{ $product->farm?->name }}</p>
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $product->product_name }}</h3>
                        <div class="flex items-baseline gap-1 mb-3">
                            <span class="text-lg font-bold text-green-700">GHS {{ number_format($product->unit_price, 2) }}</span>
                            <span class="text-xs text-gray-400">/ {{ $product->unit }}</span>
                        </div>

                        @if($product->current_stock <= 0 || ! $product->marketplace_listed)
                            <span class="text-xs text-red-500 mb-3">Out of stock</span>
                        @endif

                        <div class="mt-auto flex gap-2">
                            @if($product->current_stock > 0 && $product->marketplace_listed)
                                <button wire:click="addToCart({{ $product->id }})"
                                    wire:loading.attr="disabled"
                                    class="flex-1 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white text-sm font-medium py-2 rounded-lg transition-colors">
                                    <span wire:loading.remove wire:target="addToCart({{ $product->id }})">🛒 Add to Cart</span>
                                    <span wire:loading wire:target="addToCart({{ $product->id }})">Adding...</span>
                                </button>
                            @else
                                <a href="{{ route('farm-shop.show', $product) }}"
                                    class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium py-2 rounded-lg transition-colors">
                                    View Product
                                </a>
                            @endif

                            <button wire:click="removeFromWishlist({{ $item->id }})"
                                wire:confirm="Remove from wishlist?"
                                wire:loading.attr="disabled"
                                class="px-3 py-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors text-lg"
                                title="Remove from wishlist">
                                🗑
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
