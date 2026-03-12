<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\Hostels\Http\Livewire\HostelOccupant\Auth\ForgotPassword;
use Modules\Hostels\Http\Livewire\HostelOccupant\Auth\Login;
use Modules\Hostels\Http\Livewire\HostelOccupant\Auth\Register;
use Modules\Hostels\Http\Livewire\HostelOccupant\Auth\ResetPassword;
use Modules\Hostels\Http\Livewire\HostelOccupant\Books\Checkout as BooksCheckout;
use Modules\Hostels\Http\Livewire\HostelOccupant\Books\Index as BooksIndex;
use Modules\Hostels\Http\Livewire\HostelOccupant\Books\Orders as BooksOrders;
use Modules\Hostels\Http\Livewire\HostelOccupant\Bookings\Cancel as BookingsCancel;
use Modules\Hostels\Http\Livewire\HostelOccupant\Bookings\Create as BookingsCreate;
use Modules\Hostels\Http\Livewire\HostelOccupant\Bookings\Index as BookingsIndex;
use Modules\Hostels\Http\Livewire\HostelOccupant\Bookings\Receipt as BookingsReceipt;
use Modules\Hostels\Http\Livewire\HostelOccupant\Bookings\Show as BookingsShow;
use Modules\Hostels\Http\Livewire\HostelOccupant\Dashboard;
use Modules\Hostels\Http\Livewire\HostelOccupant\Incidents\Create as IncidentsCreate;
use Modules\Hostels\Http\Livewire\HostelOccupant\Incidents\Index as IncidentsIndex;
use Modules\Hostels\Http\Livewire\HostelOccupant\Maintenance\Create as MaintenanceCreate;
use Modules\Hostels\Http\Livewire\HostelOccupant\Maintenance\Index as MaintenanceIndex;
use Modules\Hostels\Http\Livewire\HostelOccupant\Movies\Index as MoviesIndex;
use Modules\Hostels\Http\Livewire\HostelOccupant\Movies\Request as MoviesRequest;
use Modules\Hostels\Http\Livewire\HostelOccupant\Movies\Watch as MoviesWatch;
use Modules\Hostels\Http\Livewire\HostelOccupant\Profile\Edit as ProfileEdit;
use Modules\Hostels\Http\Livewire\HostelOccupant\Restaurant\Menu as RestaurantMenu;
use Modules\Hostels\Http\Livewire\HostelOccupant\Shop\Checkout as ShopCheckout;
use Modules\Hostels\Http\Livewire\HostelOccupant\Shop\Index as ShopIndex;
use Modules\Hostels\Http\Livewire\HostelOccupant\Shop\Orders as ShopOrders;
use Modules\Hostels\Http\Livewire\HostelOccupant\Visitors\Create as VisitorsCreate;
use Modules\Hostels\Http\Livewire\HostelOccupant\Visitors\Index as VisitorsIndex;
use Modules\Hostels\Http\Livewire\HostelOccupant\WhatsAppGroups\Index as WhatsAppGroupsIndex;

Route::middleware(['web'])
    ->prefix('hostel-occupant')
    ->name('hostel_occupant.')
    ->group(function () {
        // Root redirect
        Route::get('/', function () {
            return auth('hostel_occupant')->check()
                ? redirect()->route('hostel_occupant.dashboard')
                : redirect()->route('hostel_occupant.login');
        });

        Route::get('login', Login::class)->name('login');
        Route::get('register', Register::class)->name('register');

        // Password reset
        Route::get('forgot-password', ForgotPassword::class)->name('password.request');
        Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');
    });

Route::middleware(['web', 'auth:hostel_occupant'])
    ->prefix('hostel-occupant')
    ->name('hostel_occupant.')
    ->group(function () {
        Route::post('logout', function () {
            Auth::guard('hostel_occupant')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('hostel_occupant.login');
        })->name('logout');

        Route::get('dashboard', Dashboard::class)->name('dashboard');
        Route::get('bookings', BookingsIndex::class)->name('bookings.index');
        Route::get('bookings/create', BookingsCreate::class)->name('bookings.create');
        Route::get('bookings/{booking}', BookingsShow::class)->name('bookings.show');
        Route::get('bookings/{booking}/cancel', BookingsCancel::class)->name('bookings.cancel');
        Route::get('bookings/{booking}/receipt', BookingsReceipt::class)->name('bookings.receipt');
        Route::get('maintenance', MaintenanceIndex::class)->name('maintenance.index');
        Route::get('maintenance/create', MaintenanceCreate::class)->name('maintenance.create');
        Route::get('profile', ProfileEdit::class)->name('profile.edit');
        Route::get('incidents', IncidentsIndex::class)->name('incidents.index');
        Route::get('incidents/create', IncidentsCreate::class)->name('incidents.create');
        Route::get('visitors', VisitorsIndex::class)->name('visitors.index');
        Route::get('visitors/create', VisitorsCreate::class)->name('visitors.create');

        // WhatsApp Groups
        Route::get('whatsapp-groups', WhatsAppGroupsIndex::class)->name('whatsapp-groups.index');

        // Restaurant
        Route::get('restaurant', RestaurantMenu::class)->name('restaurant.menu');

        // Shop
        Route::get('shop', ShopIndex::class)->name('shop.index');
        Route::get('shop/checkout', ShopCheckout::class)->name('shop.checkout');
        Route::get('shop/orders', ShopOrders::class)->name('shop.orders');

        // Movies
        Route::get('movies', MoviesIndex::class)->name('movies.index');
        Route::get('movies/request', MoviesRequest::class)->name('movies.request');
        Route::get('movies/{hostelMovie}', MoviesWatch::class)->name('movies.watch');

        // Books
        Route::get('books', BooksIndex::class)->name('books.index');
        Route::get('books/checkout', BooksCheckout::class)->name('books.checkout');
        Route::get('books/orders', BooksOrders::class)->name('books.orders');
    });
