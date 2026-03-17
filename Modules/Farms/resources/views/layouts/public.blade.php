@php
    $shopSettings = app(\Modules\Farms\Services\ShopSettingsService::class)->forCurrentDomain();
    $shopName     = $shopSettings->shop_name ?? 'Alpha Farms';
    $tagline      = $shopSettings->tagline ?? '';
    $primaryColor = $shopSettings->primary_color ?? '#15803d';
    $shopPhone    = $shopSettings->phone ?? '';
    $shopEmail    = $shopSettings->email ?? '';
    $shopAddress  = $shopSettings->address ?? '';
    $footerAbout  = $shopSettings->footer_about_text ?? "Fresh produce delivered directly from our farms to your table. Supporting sustainable agriculture in Ghana.";
    $fbUrl        = $shopSettings->facebook_url ?? '';
    $igUrl        = $shopSettings->instagram_url ?? '';
    $waNumber     = $shopSettings->whatsapp_number ?? '';
    $metaTitle    = $shopSettings->meta_title ?? $shopName . ' — Fresh from the Farm';
    $metaDesc     = $shopSettings->meta_description ?? '';
    $shopCustomer = auth('shop_customer')->user();
    $companyId    = $shopSettings->company_id ?? null;

    // Announcement bar — check scheduling
    $now = now();
    $annBarActive = $shopSettings->announcement_bar_active && $shopSettings->announcement_bar_text;
    if ($annBarActive && $shopSettings->announcement_bar_starts_at) {
        $annBarActive = $annBarActive && $now->gte($shopSettings->announcement_bar_starts_at);
    }
    if ($annBarActive && $shopSettings->announcement_bar_ends_at) {
        $annBarActive = $annBarActive && $now->lte($shopSettings->announcement_bar_ends_at);
    }

    // Popup — check scheduling
    $popupActive = $shopSettings->popup_active ?? false;
    if ($popupActive && $shopSettings->popup_starts_at) {
        $popupActive = $popupActive && $now->gte($shopSettings->popup_starts_at);
    }
    if ($popupActive && $shopSettings->popup_ends_at) {
        $popupActive = $popupActive && $now->lte($shopSettings->popup_ends_at);
    }
    $popupTitle   = $shopSettings->popup_title ?? '';
    $popupBody    = $shopSettings->popup_body ?? '';
    $popupCtaText = $shopSettings->popup_cta_text ?? '';
    $popupCtaUrl  = $shopSettings->popup_cta_url ?? '';

    // Custom nav items
    $customNavItems = $companyId
        ? \Modules\Farms\Models\FarmShopNavItem::where('company_id', $companyId)->active()->get()
        : collect();

    // Footer static pages
    $footerPages = $companyId
        ? \Modules\Farms\Models\FarmShopPage::where('company_id', $companyId)
            ->published()
            ->whereIn('slug', ['about-us', 'terms', 'privacy-policy'])
            ->get()
            ->keyBy('slug')
        : collect();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? $metaTitle }}</title>
    @if($metaDesc)<meta name="description" content="{{ $metaDesc }}">@endif

    <!-- PWA -->
    <link rel="manifest" href="{{ route('farm-shop.manifest') }}">
    <meta name="theme-color" content="{{ $primaryColor }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ $shopName }}">
    @if($shopSettings->favicon_path)
        <link rel="icon" type="image/x-icon" href="{{ \Illuminate\Support\Facades\Storage::url($shopSettings->favicon_path) }}">
        <link rel="apple-touch-icon" href="{{ \Illuminate\Support\Facades\Storage::url($shopSettings->favicon_path) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root { --shop-primary: {{ $primaryColor }}; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">

    {{-- Announcement Bar (with scheduling) --}}
    @if($annBarActive)
    <div class="text-white text-sm text-center py-2 px-4 font-medium"
         style="background-color: {{ $primaryColor }}; filter: brightness(0.85);">
        {{ $shopSettings->announcement_bar_text }}
    </div>
    @endif

    <!-- Navigation -->
    <nav class="shadow-lg sticky top-0 z-50" style="background-color: {{ $primaryColor }};">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo + Nav Links -->
                <div class="flex items-center">
                    <a href="{{ route('farm-shop.index') }}" class="flex items-center gap-2">
                        <span class="text-2xl">🌿</span>
                        <span class="text-xl font-bold text-white">{{ $shopName }}</span>
                    </a>
                    <div class="hidden sm:ml-8 sm:flex sm:space-x-5">
                        <a href="{{ route('farm-shop.index') }}" class="text-green-100 hover:text-white px-1 pt-1 text-sm font-medium transition-colors">Shop</a>
                        <a href="{{ route('farm-shop.track') }}" class="text-green-100 hover:text-white px-1 pt-1 text-sm font-medium transition-colors">Track Order</a>
                        <a href="{{ route('farm-shop.blog.index') }}" class="text-green-100 hover:text-white px-1 pt-1 text-sm font-medium transition-colors">Blog</a>
                        <a href="{{ route('farm-shop.b2b.apply') }}" class="text-yellow-200 hover:text-white px-1 pt-1 text-sm font-medium transition-colors">🏢 For Business</a>
                        @if($waNumber)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $waNumber) }}" target="_blank"
                           class="text-green-100 hover:text-white px-1 pt-1 text-sm font-medium transition-colors">
                            💬 WhatsApp
                        </a>
                        @endif
                        {{-- Custom nav items from DB --}}
                        @foreach($customNavItems as $navItem)
                        <a href="{{ $navItem->url }}"
                           {{ $navItem->opens_blank ? 'target="_blank" rel="noopener"' : '' }}
                           class="text-green-100 hover:text-white px-1 pt-1 text-sm font-medium transition-colors">
                            {{ $navItem->label }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Cart + Customer Account -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('farm-shop.cart') }}" class="relative flex items-center gap-1.5 text-white hover:text-green-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-9H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="text-sm font-medium hidden sm:inline">Cart</span>
                        @if(($cartCount = count(session('farm_shop_cart.items', []))) > 0)
                            <span class="absolute -top-2 -right-2 bg-yellow-400 text-green-900 text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    @if($shopCustomer)
                        <div class="hidden sm:flex items-center gap-3">
                            <a href="{{ route('farm-shop.my-orders') }}" class="text-green-100 hover:text-white text-sm font-medium transition-colors">My Orders</a>
                            <a href="{{ route('farm-shop.my-subscriptions') }}" class="text-green-100 hover:text-white text-sm font-medium transition-colors">Subscriptions</a>
                            <a href="{{ route('farm-shop.my-account') }}" class="text-green-100 hover:text-white text-sm font-medium transition-colors">Account</a>
                            <form method="POST" action="{{ route('farm-shop.logout') }}">
                                @csrf
                                <button type="submit" class="text-green-100 hover:text-white text-sm font-medium transition-colors">Sign Out</button>
                            </form>
                        </div>
                    @else
                        <div class="hidden sm:flex items-center gap-3">
                            <a href="{{ route('farm-shop.login') }}" class="text-green-100 hover:text-white text-sm font-medium transition-colors">Sign In</a>
                            <a href="{{ route('farm-shop.register') }}" class="bg-white text-green-700 hover:bg-green-50 text-sm font-semibold px-3 py-1.5 rounded-lg transition-colors">Register</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="text-green-100 mt-16" style="background-color: color-mix(in srgb, {{ $primaryColor }} 90%, black 10%);">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-2xl">🌿</span>
                        <h3 class="text-lg font-bold text-white">{{ $shopName }}</h3>
                    </div>
                    <p class="text-green-300 text-sm">{{ $footerAbout }}</p>
                    @if($fbUrl || $igUrl)
                    <div class="flex gap-3 mt-4">
                        @if($fbUrl)<a href="{{ $fbUrl }}" target="_blank" class="text-green-300 hover:text-white transition-colors text-sm">Facebook</a>@endif
                        @if($igUrl)<a href="{{ $igUrl }}" target="_blank" class="text-green-300 hover:text-white transition-colors text-sm">Instagram</a>@endif
                    </div>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-green-300 uppercase tracking-wider mb-4">Shop</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('farm-shop.index') }}" class="text-green-200 hover:text-white text-sm transition-colors">Browse Produce</a></li>
                        <li><a href="{{ route('farm-shop.harvest-calendar') }}" class="text-green-200 hover:text-white text-sm transition-colors">Harvest Calendar</a></li>
                        <li><a href="{{ route('farm-shop.cart') }}" class="text-green-200 hover:text-white text-sm transition-colors">My Cart</a></li>
                        <li><a href="{{ route('farm-shop.track') }}" class="text-green-200 hover:text-white text-sm transition-colors">Track Order</a></li>
                        @if($shopCustomer)
                        <li><a href="{{ route('farm-shop.my-orders') }}" class="text-green-200 hover:text-white text-sm transition-colors">My Orders</a></li>
                        <li><a href="{{ route('farm-shop.my-subscriptions') }}" class="text-green-200 hover:text-white text-sm transition-colors">My Subscriptions</a></li>
                        <li><a href="{{ route('farm-shop.my-account') }}" class="text-green-200 hover:text-white text-sm transition-colors">My Account</a></li>
                        @else
                        <li><a href="{{ route('farm-shop.register') }}" class="text-green-200 hover:text-white text-sm transition-colors">Create Account</a></li>
                        @endif
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-green-300 uppercase tracking-wider mb-4">Info</h3>
                    <ul class="space-y-3 text-sm text-green-200">
                        @if($shopAddress)<li>📍 {{ $shopAddress }}</li>@endif
                        @if($shopPhone)<li>📞 <a href="tel:{{ $shopPhone }}" class="hover:text-white">{{ $shopPhone }}</a></li>@endif
                        @if($shopEmail)<li>📧 <a href="mailto:{{ $shopEmail }}" class="hover:text-white">{{ $shopEmail }}</a></li>@endif
                        @if($waNumber)<li>💬 <a href="https://wa.me/{{ preg_replace('/\D/', '', $waNumber) }}" target="_blank" class="hover:text-white">WhatsApp Us</a></li>@endif
                        {{-- Static pages from DB --}}
                        @if(isset($footerPages['about-us']))
                        <li><a href="{{ route('farm-shop.page.show', 'about-us') }}" class="hover:text-white transition-colors">About Us</a></li>
                        @endif
                        @if(isset($footerPages['terms']))
                        <li><a href="{{ route('farm-shop.page.show', 'terms') }}" class="hover:text-white transition-colors">Terms & Conditions</a></li>
                        @endif
                        @if(isset($footerPages['privacy-policy']))
                        <li><a href="{{ route('farm-shop.page.show', 'privacy-policy') }}" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="mt-10 border-t border-green-700 pt-8 text-center">
                <p class="text-green-400 text-sm">&copy; {{ date('Y') }} {{ $shopName }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    {{-- Popup Modal (cookie-based dismiss, scheduled) --}}
    @if($popupActive && $popupTitle)
    <div x-data="{
            open: false,
            init() {
                const key = 'farm_shop_popup_dismissed_{{ $companyId }}';
                if (!localStorage.getItem(key)) {
                    setTimeout(() => this.open = true, 1500);
                }
            },
            dismiss() {
                const key = 'farm_shop_popup_dismissed_{{ $companyId }}';
                localStorage.setItem(key, '1');
                this.open = false;
            }
         }"
         x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[200] flex items-center justify-center bg-black/50 px-4"
         style="display: none;">
        <div x-on:click.outside="dismiss()"
             class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 relative"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <button x-on:click="dismiss()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <h2 class="text-xl font-bold text-gray-900 mb-3">{{ $popupTitle }}</h2>
            @if($popupBody)
                <p class="text-gray-600 text-sm mb-5">{{ $popupBody }}</p>
            @endif
            <div class="flex gap-3">
                @if($popupCtaText && $popupCtaUrl)
                <a href="{{ $popupCtaUrl }}"
                   x-on:click="dismiss()"
                   class="flex-1 text-center bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-5 rounded-xl transition-colors text-sm">
                    {{ $popupCtaText }}
                </a>
                @endif
                <button x-on:click="dismiss()"
                        class="flex-1 text-center border border-gray-300 text-gray-600 hover:text-gray-800 font-medium py-2.5 px-5 rounded-xl transition-colors text-sm">
                    Maybe Later
                </button>
            </div>
        </div>
    </div>
    @endif

    @livewireScripts

    {{-- PWA Service Worker registration --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/farm-shop-sw.js', { scope: '/farm-shop/' })
                    .catch(function () { /* sw optional — silently fail */ });
            });
        }
    </script>
</body>
</html>
