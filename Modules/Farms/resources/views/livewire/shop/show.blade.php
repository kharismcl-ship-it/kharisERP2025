<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <a href="{{ route('farm-shop.index') }}" class="inline-flex items-center gap-1.5 text-green-700 hover:text-green-900 text-sm font-medium mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Shop
    </a>

    @if(session('error'))
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">
            {{-- Image Gallery --}}
            @php $images = $product->images ?? []; @endphp
            @if(count($images))
            <div class="h-72 md:h-auto relative overflow-hidden bg-gray-100">
                <img src="{{ \Illuminate\Support\Facades\Storage::url($images[0]) }}"
                     alt="{{ $product->product_name }}"
                     class="w-full h-full object-cover" />
                @if(count($images) > 1)
                <div class="absolute bottom-3 left-3 flex gap-2">
                    @foreach(array_slice($images, 1, 4) as $img)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($img) }}"
                         alt="{{ $product->product_name }}"
                         class="w-12 h-12 rounded-lg object-cover border-2 border-white shadow" />
                    @endforeach
                </div>
                @endif
            </div>
            @else
            <div class="h-72 md:h-auto bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                <span class="text-9xl">🌿</span>
            </div>
            @endif

            {{-- Details --}}
            <div class="p-8 flex flex-col">
                <div class="mb-1">
                    <span class="text-xs text-green-600 font-semibold uppercase tracking-wider">{{ $product->farm?->name }}</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ $product->product_name }}</h1>

                @if($product->description)
                    <p class="text-gray-600 mb-4">{{ $product->description }}</p>
                @endif

                {{-- Flash Sale badge --}}
                @if($isOnSale)
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1 bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                            🏷️ SALE
                        </span>
                        @if($saleEndsAt)
                            <span class="text-xs text-red-600 font-medium"
                                  x-data="{
                                      ends: new Date('{{ $saleEndsAt }}'),
                                      label: '',
                                      update() {
                                          const diff = this.ends - new Date();
                                          if (diff <= 0) { this.label = 'Sale ended'; return; }
                                          const h = Math.floor(diff / 3600000);
                                          const m = Math.floor((diff % 3600000) / 60000);
                                          const s = Math.floor((diff % 60000) / 1000);
                                          this.label = h > 0
                                              ? `Ends in ${h}h ${m}m`
                                              : `Ends in ${m}m ${s}s`;
                                      }
                                  }"
                                  x-init="update(); setInterval(() => update(), 1000)"
                                  x-text="label">
                            </span>
                        @endif
                    </div>
                @endif

                {{-- B2B wholesale badge --}}
                @if($isB2b && $b2bDiscountPct > 0)
                    <div class="inline-flex items-center gap-1.5 bg-blue-50 border border-blue-200 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full mb-2">
                        🏢 Wholesale Price — {{ $b2bDiscountPct }}% off
                    </div>
                @endif

                {{-- Price display —  shows effective tier/sale price reactively --}}
                <div class="flex items-baseline gap-2 mb-1">
                    @if($isOnSale)
                        <span class="text-3xl font-bold text-red-600">GHS {{ number_format($effectivePrice, 2) }}</span>
                        <span class="text-lg line-through text-gray-400">GHS {{ number_format($originalUnitPrice, 2) }}</span>
                    @else
                        <span class="text-3xl font-bold text-green-700">GHS {{ number_format($effectivePrice, 2) }}</span>
                    @endif
                    <span class="text-gray-400 text-sm">per {{ $product->unit }}</span>
                    @if($activeTierLabel)
                        <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">{{ $activeTierLabel }}</span>
                    @endif
                </div>

                {{-- Market price comparison --}}
                @if($product->market_price && $product->market_price > $effectivePrice)
                    @php $savings = $product->market_price - $effectivePrice; $savingsPct = round($savings / $product->market_price * 100); @endphp
                    <div class="inline-flex items-center gap-2 mb-2 bg-amber-50 border border-amber-200 rounded-lg px-3 py-1.5 text-xs">
                        <span class="text-gray-500">Market (Makola):</span>
                        <span class="line-through text-gray-400">GHS {{ number_format($product->market_price, 2) }}</span>
                        <span class="text-green-700 font-bold">{{ $savingsPct }}% cheaper here!</span>
                    </div>
                @endif

                {{-- Stock status badge --}}
                @php $outOfStock = $product->current_stock <= 0 || $product->status === 'depleted'; @endphp
                <div class="flex items-center gap-2 mb-4">
                    @if($outOfStock)
                        <span class="inline-flex items-center gap-1 text-sm text-red-700 bg-red-50 px-2.5 py-0.5 rounded-full font-medium">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span> Out of Stock
                        </span>
                    @elseif($product->status === 'in_stock')
                        <span class="inline-flex items-center gap-1 text-sm text-green-700 bg-green-50 px-2.5 py-0.5 rounded-full font-medium">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span> In Stock
                        </span>
                        <span class="text-sm text-gray-400">{{ number_format($product->current_stock, 1) }} {{ $product->unit }} available</span>
                    @else
                        <span class="inline-flex items-center gap-1 text-sm text-yellow-700 bg-yellow-50 px-2.5 py-0.5 rounded-full font-medium">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full"></span> Low Stock
                        </span>
                        <span class="text-sm text-gray-400">{{ number_format($product->current_stock, 1) }} {{ $product->unit }} left</span>
                    @endif
                </div>

                @if(! $outOfStock)
                    {{-- Bulk pricing table --}}
                    @if($product->priceTiers->isNotEmpty())
                        <div class="mb-4 bg-blue-50 rounded-xl p-3">
                            <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider mb-2">Bulk Pricing</p>
                            <div class="space-y-1">
                                <div class="flex justify-between text-xs text-gray-600">
                                    <span>Regular (any qty)</span>
                                    <span class="font-medium">GHS {{ number_format($product->unit_price, 2) }} / {{ $product->unit }}</span>
                                </div>
                                @foreach($product->priceTiers as $tier)
                                <div class="flex justify-between text-xs {{ $activeTierLabel === $tier->label && $effectivePrice == (float) $tier->price_per_unit ? 'text-blue-700 font-semibold' : 'text-gray-600' }}">
                                    <span>{{ $tier->label ?? (number_format($tier->min_quantity, 0) . '+ ' . $product->unit) }}</span>
                                    <span class="font-medium">GHS {{ number_format($tier->price_per_unit, 2) }} / {{ $product->unit }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Quantity + Weight Helpers --}}
                    <div class="mb-4 space-y-3">
                        {{-- Quick weight presets (shown for kg / g / lb units) --}}
                        @php
                            $weightUnit  = strtolower(trim($product->unit));
                            $isWeightUnit = in_array($weightUnit, ['kg', 'g', 'lb', 'lbs', 'gram', 'grams', 'kilogram', 'kilograms']);
                            $presets = match(true) {
                                in_array($weightUnit, ['kg', 'kilogram', 'kilograms']) => ['0.25' => '250g', '0.5' => '500g', '1' => '1 kg', '2' => '2 kg', '5' => '5 kg'],
                                in_array($weightUnit, ['g', 'gram', 'grams'])          => ['100' => '100g', '250' => '250g', '500' => '500g', '1000' => '1 kg'],
                                in_array($weightUnit, ['lb', 'lbs'])                   => ['0.5' => '½ lb', '1' => '1 lb', '2' => '2 lbs', '5' => '5 lbs'],
                                default                                                 => [],
                            };
                        @endphp
                        @if(count($presets))
                        <div>
                            <p class="text-xs text-gray-500 mb-1.5">Quick select:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($presets as $val => $label)
                                <button type="button"
                                    wire:click="$set('quantity', {{ $val }})"
                                    class="text-xs border rounded-lg px-3 py-1.5 transition-colors
                                           {{ (string) $quantity == $val ? 'bg-green-600 text-white border-green-600' : 'border-gray-300 text-gray-600 hover:border-green-400' }}">
                                    {{ $label }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Quantity ({{ $product->unit }})</label>
                            <input
                                wire:model.live="quantity"
                                type="number"
                                min="{{ $product->min_order_quantity > 0 ? $product->min_order_quantity : 0.1 }}"
                                max="{{ $product->current_stock }}"
                                step="{{ $isWeightUnit ? '0.01' : '1' }}"
                                class="w-28 rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-center text-sm"
                            />
                        </div>

                        @if($product->min_order_quantity > 0)
                            <p class="text-xs text-amber-600">Minimum order: {{ number_format($product->min_order_quantity, 1) }} {{ $product->unit }}</p>
                        @endif

                        {{-- Live total price --}}
                        <div class="bg-green-50 rounded-xl px-4 py-3 flex items-center justify-between">
                            <span class="text-sm text-gray-600">
                                {{ number_format($quantity, in_array($weightUnit ?? '', ['kg','kilogram','kilograms','g','gram','grams','lb','lbs']) ? 2 : 1) }}
                                {{ $product->unit }} × GHS {{ number_format($effectivePrice, 2) }}
                            </span>
                            <span class="text-lg font-bold text-green-700">
                                GHS {{ number_format($quantity * $effectivePrice, 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button
                            wire:click="addToCart"
                            wire:loading.attr="disabled"
                            class="flex-1 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-semibold py-3 px-6 rounded-xl transition-colors flex items-center justify-center gap-2"
                        >
                            <span wire:loading.remove wire:target="addToCart">🛒 Add to Cart</span>
                            <span wire:loading wire:target="addToCart">Adding...</span>
                        </button>

                        {{-- Wishlist toggle --}}
                        <button
                            wire:click="toggleWishlist"
                            wire:loading.attr="disabled"
                            title="{{ $inWishlist ? 'Remove from wishlist' : 'Save to wishlist' }}"
                            class="p-3 rounded-xl border transition-colors
                                {{ $inWishlist ? 'border-red-300 bg-red-50 text-red-500 hover:bg-red-100' : 'border-gray-300 text-gray-400 hover:text-red-400 hover:border-red-300 hover:bg-red-50' }}"
                        >
                            {{ $inWishlist ? '♥' : '♡' }}
                        </button>
                    </div>
                @else
                    {{-- OUT OF STOCK: Notify Me + Wishlist --}}
                    <div class="mt-2">
                        @if($subscribed || $alreadySubscribed)
                            <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-4 text-sm text-green-800">
                                ✅ You're on the list! We'll notify you as soon as this is back in stock.
                            </div>
                        @else
                            <p class="text-sm text-gray-600 mb-3">Get notified when this is back in stock:</p>
                            @if(session('restock_error'))
                                <p class="text-xs text-red-600 mb-2">{{ session('restock_error') }}</p>
                            @endif
                            @if(! auth('shop_customer')->check())
                                <div class="flex gap-2">
                                    <input
                                        wire:model="subscribeEmail"
                                        type="email"
                                        placeholder="Your email address"
                                        class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"
                                    />
                                    <button wire:click="subscribeToRestock"
                                        wire:loading.attr="disabled"
                                        class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                                        <span wire:loading.remove wire:target="subscribeToRestock">Notify Me</span>
                                        <span wire:loading wire:target="subscribeToRestock">...</span>
                                    </button>
                                </div>
                            @else
                                <button wire:click="subscribeToRestock"
                                    wire:loading.attr="disabled"
                                    class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-semibold py-3 px-6 rounded-xl transition-colors">
                                    <span wire:loading.remove wire:target="subscribeToRestock">🔔 Notify Me When Available</span>
                                    <span wire:loading wire:target="subscribeToRestock">Subscribing...</span>
                                </button>
                            @endif
                        @endif

                        {{-- Wishlist toggle even when out of stock --}}
                        <button
                            wire:click="toggleWishlist"
                            wire:loading.attr="disabled"
                            class="mt-3 w-full py-2.5 rounded-xl border text-sm font-medium transition-colors
                                {{ $inWishlist ? 'border-red-300 bg-red-50 text-red-600 hover:bg-red-100' : 'border-gray-300 text-gray-500 hover:border-red-300 hover:text-red-500' }}"
                        >
                            {{ $inWishlist ? '♥ Saved to Wishlist' : '♡ Save to Wishlist' }}
                        </button>
                    </div>
                @endif

                @if($product->harvest_date)
                    <p class="mt-4 text-xs text-gray-400">Harvested: {{ $product->harvest_date->format('M d, Y') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Reviews Section --}}
    <div class="mt-8 bg-white rounded-2xl shadow-sm p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Customer Reviews</h2>
                @if($reviewCount > 0)
                    <div class="flex items-center gap-2 mt-1">
                        <div class="flex text-yellow-400 text-lg">
                            @for($s = 1; $s <= 5; $s++)
                                {{ $s <= round($avgRating) ? '★' : '☆' }}
                            @endfor
                        </div>
                        <span class="text-sm text-gray-500">{{ $avgRating }} / 5 ({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})</span>
                    </div>
                @else
                    <p class="text-sm text-gray-400 mt-1">No reviews yet. Be the first!</p>
                @endif
            </div>

            @if(auth('shop_customer')->check() && ! $alreadyReviewed && ! $outOfStock)
                <button wire:click="$toggle('showReviewForm')"
                    class="text-sm font-medium text-green-700 hover:text-green-900 border border-green-300 px-4 py-2 rounded-lg">
                    ✏️ Write a Review
                </button>
            @elseif(! auth('shop_customer')->check())
                <a href="{{ route('farm-shop.login') }}"
                    class="text-sm font-medium text-green-700 hover:text-green-900 border border-green-300 px-4 py-2 rounded-lg">
                    Sign in to Review
                </a>
            @endif
        </div>

        @if(session('review_success'))
            <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('review_success') }}
            </div>
        @endif

        {{-- Review Form --}}
        @if($showReviewForm && auth('shop_customer')->check() && ! $alreadyReviewed)
            <div class="mb-6 bg-gray-50 rounded-xl p-6 border border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-4">Your Review</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating *</label>
                    <div class="flex gap-2">
                        @for($s = 1; $s <= 5; $s++)
                            <button wire:click="$set('rating', {{ $s }})"
                                class="text-3xl transition-transform hover:scale-110 {{ $s <= $rating ? 'text-yellow-400' : 'text-gray-300' }}">
                                ★
                            </button>
                        @endfor
                    </div>
                    @error('rating') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Review (optional)</label>
                    <textarea wire:model="reviewText" rows="3"
                        placeholder="Share your experience with this product..."
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                    @error('reviewText') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3">
                    <button wire:click="submitReview"
                        wire:loading.attr="disabled"
                        class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-semibold py-2.5 px-6 rounded-xl transition-colors">
                        <span wire:loading.remove wire:target="submitReview">Submit Review</span>
                        <span wire:loading wire:target="submitReview">Submitting...</span>
                    </button>
                    <button wire:click="$set('showReviewForm', false)"
                        class="text-gray-500 hover:text-gray-700 text-sm font-medium py-2.5 px-4">
                        Cancel
                    </button>
                </div>
            </div>
        @endif

        {{-- Reviews List --}}
        @if($reviews->isNotEmpty())
            <div class="space-y-5">
                @foreach($reviews as $review)
                    <div class="border-b border-gray-100 pb-5 last:border-0">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-900 text-sm">{{ $review->reviewer_name }}</span>
                                    @if($review->farm_order_id)
                                        <span class="text-xs bg-green-50 text-green-700 px-2 py-0.5 rounded-full font-medium">✓ Verified Purchase</span>
                                    @endif
                                </div>
                                <div class="flex text-yellow-400 mt-0.5">
                                    @for($s = 1; $s <= 5; $s++)
                                        <span class="text-sm">{{ $s <= $review->rating ? '★' : '☆' }}</span>
                                    @endfor
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        @if($review->review_text)
                            <p class="mt-2 text-sm text-gray-600">{{ $review->review_text }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
