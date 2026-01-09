<?php

use Illuminate\Support\Facades\Route;
use Modules\Hostels\Http\Livewire\HostelOccupant\Auth\Login;
use Modules\Hostels\Http\Livewire\HostelOccupant\Auth\Register;
use Modules\Hostels\Http\Livewire\HostelOccupant\Bookings\Create as BookingsCreate;
use Modules\Hostels\Http\Livewire\HostelOccupant\Bookings\Index as BookingsIndex;
use Modules\Hostels\Http\Livewire\HostelOccupant\Bookings\Show as BookingsShow;
use Modules\Hostels\Http\Livewire\HostelOccupant\Dashboard;
use Modules\Hostels\Http\Livewire\HostelOccupant\Maintenance\Create as MaintenanceCreate;
use Modules\Hostels\Http\Livewire\HostelOccupant\Maintenance\Index as MaintenanceIndex;
use Modules\Hostels\Http\Livewire\HostelOccupant\Profile\Edit as ProfileEdit;

Route::middleware(['web'])
    ->prefix('hostel-occupant')
    ->name('hostel_occupant.')
    ->group(function () {
        Route::get('login', Login::class)->name('login');
        Route::get('register', Register::class)->name('register');
    });

Route::middleware(['web', 'auth:hostel_occupant'])
    ->prefix('hostel-occupant')
    ->name('hostel_occupant.')
    ->group(function () {
        Route::get('dashboard', Dashboard::class)->name('dashboard');
        Route::get('bookings', BookingsIndex::class)->name('bookings.index');
        Route::get('bookings/create', BookingsCreate::class)->name('bookings.create');
        Route::get('bookings/{booking}', BookingsShow::class)->name('bookings.show');
        Route::get('maintenance', MaintenanceIndex::class)->name('maintenance.index');
        Route::get('maintenance/create', MaintenanceCreate::class)->name('maintenance.create');
        Route::get('profile', ProfileEdit::class)->name('profile.edit');
    });
