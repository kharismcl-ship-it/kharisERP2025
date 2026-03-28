<?php

use Illuminate\Support\Facades\Route;
use Modules\Farms\Http\Livewire\Shop\Auth\ForgotPassword as ShopForgotPassword;
use Modules\Farms\Http\Livewire\Shop\Auth\Login as ShopLogin;
use Modules\Farms\Http\Livewire\Shop\Auth\Register as ShopRegister;
use Modules\Farms\Http\Livewire\Shop\Auth\ResetPassword as ShopResetPassword;
use Modules\Farms\Http\Livewire\Shop\B2bRegister;
use Modules\Farms\Http\Livewire\Shop\BlogIndex;
use Modules\Farms\Http\Livewire\Shop\BlogShow;
use Modules\Farms\Http\Livewire\Shop\FarmProfile;
use Modules\Farms\Http\Livewire\Shop\BundleShow;
use Modules\Farms\Http\Livewire\Shop\FarmShopPage as FarmShopPageComponent;
use Modules\Farms\Http\Livewire\Shop\HarvestCalendar;
use Modules\Farms\Http\Livewire\Shop\MyProfile;
use Modules\Farms\Http\Livewire\Shop\MySubscriptions;
use Modules\Farms\Http\Livewire\Shop\MyWishlist;
use Modules\Farms\Http\Livewire\Shop\OrderReceipt;
use Modules\Farms\Http\Livewire\Shop\RequestRefund;
use Modules\Farms\Http\Livewire\Shop\Cart as ShopCart;
use Modules\Farms\Http\Livewire\Shop\Checkout as ShopCheckout;
use Modules\Farms\Http\Livewire\Shop\Index as ShopIndex;
use Modules\Farms\Http\Livewire\Shop\MyOrders;
use Modules\Farms\Http\Livewire\Shop\OrderConfirmation;
use Modules\Farms\Http\Livewire\Shop\OrderPayment;
use Modules\Farms\Http\Livewire\Shop\OrderPaymentReturn;
use Modules\Farms\Http\Livewire\Shop\OrderTracking;
use Modules\Farms\Http\Livewire\Shop\Show as ShopShow;
use Modules\Farms\Http\Livewire\Attendance\Index as AttendanceIndex;
use Modules\Farms\Http\Livewire\Crops\Index as CropsIndex;
use Modules\Farms\Http\Livewire\Crops\RecordHarvest;
use Modules\Farms\Http\Livewire\Crops\Show as CropShow;
use Modules\Farms\Http\Livewire\DailyReports\Create as DailyReportsCreate;
use Modules\Farms\Http\Livewire\DailyReports\Index as DailyReportsIndex;
use Modules\Farms\Http\Livewire\DailyReports\Show as DailyReportsShow;
use Modules\Farms\Http\Livewire\FarmDashboard;
use Modules\Farms\Http\Livewire\FarmIndex;
use Modules\Farms\Http\Livewire\FarmMap;
use Modules\Farms\Http\Livewire\Livestock\Index as LivestockIndex;
use Modules\Farms\Http\Livewire\Livestock\Show as LivestockShow;
use Modules\Farms\Http\Livewire\Reports\Index as ReportsIndex;
use Modules\Farms\Http\Livewire\Requests\Create as RequestsCreate;
use Modules\Farms\Http\Livewire\Requests\Index as RequestsIndex;
use Modules\Farms\Http\Livewire\Requests\Show as RequestsShow;
use Modules\Farms\Http\Livewire\Tasks\Index as TasksIndex;

// ── Farm USSD webhook (Africa's Talking compatible) ──────────────────────────
Route::post('/farm-ussd', [\Modules\Farms\Http\Controllers\FarmUssdController::class, 'handle'])
    ->name('farms.ussd.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// ── PWA assets (served from root path so SW can scope /farm-shop/) ──────────
Route::get('/farm-shop-sw.js', function () {
    $content = <<<'JS'
const CACHE = 'farm-shop-v1';
const PRECACHE = ['/farm-shop/', '/farm-shop/cart'];

self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE).then(cache => cache.addAll(PRECACHE)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys().then(keys => Promise.all(
            keys.filter(k => k !== CACHE).map(k => caches.delete(k))
        )).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', e => {
    if (e.request.method !== 'GET') return;
    if (!e.request.url.includes('/farm-shop')) return;
    e.respondWith(
        fetch(e.request).catch(() => caches.match(e.request))
    );
});
JS;
    return response($content, 200, [
        'Content-Type' => 'application/javascript',
        'Service-Worker-Allowed' => '/farm-shop/',
    ]);
})->name('farm-shop.sw');

// ── Public Farm Shop ──────────────────────────────────────────────────────
Route::middleware(['web'])
    ->prefix('farm-shop')
    ->name('farm-shop.')
    ->group(function () {
        Route::get('/', ShopIndex::class)->name('index');

        // PWA manifest (dynamic — uses shop settings)
        Route::get('/manifest.json', function () {
            $settings = app(\Modules\Farms\Services\ShopSettingsService::class)->forCurrentDomain();
            $shopName = $settings->shop_name ?? 'Farm Shop';
            $color    = $settings->primary_color ?? '#15803d';
            return response()->json([
                'name'             => $shopName,
                'short_name'       => $shopName,
                'description'      => $settings->tagline ?? 'Fresh produce from the farm',
                'start_url'        => '/farm-shop/',
                'display'          => 'standalone',
                'background_color' => '#ffffff',
                'theme_color'      => $color,
                'icons'            => [
                    ['src' => '/img/icons/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png'],
                    ['src' => '/img/icons/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png'],
                ],
                'categories'       => ['shopping', 'food'],
                'screenshots'      => [],
            ])->header('Content-Type', 'application/manifest+json');
        })->name('manifest');
        Route::get('/products/{product}', ShopShow::class)->name('show');
        Route::get('/cart', ShopCart::class)->name('cart');
        Route::get('/checkout', ShopCheckout::class)->name('checkout');
        Route::get('/orders/{order}/payment', OrderPayment::class)->name('order.payment');
        Route::get('/orders/{order}/payment/return', OrderPaymentReturn::class)->name('order.payment-return');
        Route::get('/orders/{order}/confirmation', OrderConfirmation::class)->name('order.confirmation');
        Route::get('/orders/{order}/receipt', OrderReceipt::class)->name('order.receipt');
        Route::get('/orders/track', OrderTracking::class)->name('track');
        Route::get('/harvest-calendar', HarvestCalendar::class)->name('harvest-calendar');
        Route::get('/bundles/{bundle}', BundleShow::class)->name('bundle.show');
        Route::get('/pages/{slug}', FarmShopPageComponent::class)->name('page.show');
        Route::get('/blog', BlogIndex::class)->name('blog.index');
        Route::get('/blog/{slug}', BlogShow::class)->name('blog.show');
        Route::get('/farms/{slug}', FarmProfile::class)->name('farm.profile');
        Route::get('/b2b/apply', B2bRegister::class)->name('b2b.apply');

        // Customer auth (guest only)
        Route::middleware('guest:shop_customer')->group(function () {
            Route::get('/login', ShopLogin::class)->name('login');
            Route::get('/register', ShopRegister::class)->name('register');
            Route::get('/forgot-password', ShopForgotPassword::class)->name('password.request');
            Route::get('/reset-password/{token}', ShopResetPassword::class)->name('password.reset');
        });

        // Logout (POST, no middleware)
        Route::post('/logout', function () {
            auth('shop_customer')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('farm-shop.index');
        })->name('logout');

        // Customer account (authenticated)
        Route::middleware('auth:shop_customer')->group(function () {
            Route::get('/my-orders', MyOrders::class)->name('my-orders');
            Route::get('/my-account', MyProfile::class)->name('my-account');
            Route::get('/my-wishlist', MyWishlist::class)->name('my-wishlist');
            Route::get('/my-subscriptions', MySubscriptions::class)->name('my-subscriptions');
            Route::get('/orders/{order}/refund', RequestRefund::class)->name('order.refund');
        });
    });

// ── Authenticated Farm Portal ──────────────────────────────────────────────
Route::middleware(['web', 'auth', 'set-company:farms'])
    ->prefix('farms')
    ->name('farms.')
    ->group(function () {
        Route::get('/', FarmIndex::class)->name('index');

        Route::prefix('{farm:slug}')->group(function () {
            Route::get('/', FarmDashboard::class)->name('dashboard');

            // Tasks
            Route::get('/tasks', TasksIndex::class)->name('tasks.index');

            // Daily Reports
            Route::get('/daily-reports', DailyReportsIndex::class)->name('daily-reports.index');
            Route::get('/daily-reports/create', DailyReportsCreate::class)->name('daily-reports.create');
            Route::get('/daily-reports/{report}', DailyReportsShow::class)->name('daily-reports.show');

            // Crops
            Route::get('/crops', CropsIndex::class)->name('crops.index');
            Route::get('/crops/{cropCycle}', CropShow::class)->name('crops.show');
            Route::get('/crops/{cropCycle}/harvest', RecordHarvest::class)->name('crops.harvest');

            // Livestock
            Route::get('/livestock', LivestockIndex::class)->name('livestock.index');
            Route::get('/livestock/{batch}', LivestockShow::class)->name('livestock.show');

            // Requests
            Route::get('/requests', RequestsIndex::class)->name('requests.index');
            Route::get('/requests/create', RequestsCreate::class)->name('requests.create');
            Route::get('/requests/{request}', RequestsShow::class)->name('requests.show');

            // Attendance
            Route::get('/attendance', AttendanceIndex::class)->name('attendance.index');

            // Map
            Route::get('/map', FarmMap::class)->name('map');

            // Reports
            Route::get('/reports', ReportsIndex::class)->name('reports.index');
        });
    });
