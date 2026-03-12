<?php

use Illuminate\Support\Facades\Route;
use Modules\Hostels\Http\Livewire\Admin\BedList;
use Modules\Hostels\Http\Livewire\Admin\BookingApproval;
use Modules\Hostels\Http\Livewire\Admin\BookingChangeRequests;
use Modules\Hostels\Http\Livewire\Admin\BookingList;
use Modules\Hostels\Http\Livewire\Admin\Bookings\Create as AdminBookingsCreate;
use Modules\Hostels\Http\Livewire\Admin\Bookings\Show as AdminBookingsShow;
use Modules\Hostels\Http\Livewire\Admin\CheckIn;
use Modules\Hostels\Http\Livewire\Admin\CheckOut;
use Modules\Hostels\Http\Livewire\Admin\Dashboard;
use Modules\Hostels\Http\Livewire\Admin\DepositCollection;
use Modules\Hostels\Http\Livewire\Admin\HostelChargeList;
use Modules\Hostels\Http\Livewire\Admin\HostelList;
use Modules\Hostels\Http\Livewire\Admin\HostelOccupantList;
use Modules\Hostels\Http\Livewire\Admin\HostelWhatsAppGroupList;
use Modules\Hostels\Http\Livewire\Admin\Incidents\Index as AdminIncidentsIndex;
use Modules\Hostels\Http\Livewire\Admin\Maintenance\Index as AdminMaintenanceIndex;
use Modules\Hostels\Http\Livewire\Admin\Reports\Index as AdminReportsIndex;
use Modules\Hostels\Http\Livewire\Admin\RoomList;
use Modules\Hostels\Http\Livewire\Admin\Visitors\Index as AdminVisitorsIndex;
use Modules\Hostels\Http\Livewire\Admin\WhatsAppGroupMessages;
// Public routes
use Modules\Hostels\Http\Livewire\Public\BookingChangeRequest;
use Modules\Hostels\Http\Livewire\Public\BookingConfirmation;
use Modules\Hostels\Http\Livewire\Public\BookingPayment;
use Modules\Hostels\Http\Livewire\Public\BookingPaymentFailed;
use Modules\Hostels\Http\Livewire\Public\BookingPaymentReturn;
use Modules\Hostels\Http\Livewire\Public\BookingWizard;
use Modules\Hostels\Http\Livewire\Public\Index as PublicIndex;
use Modules\Hostels\Http\Livewire\Public\Show as PublicShow;

// ── Public ────────────────────────────────────────────────────────────────────
Route::middleware(['web'])
    ->prefix('hostels')
    ->name('hostels.public.')
    ->group(function () {
        Route::get('/', PublicIndex::class)->name('index');
        Route::get('/{hostel:slug}', PublicShow::class)->name('show');
        Route::get('/{hostel:slug}/book', BookingWizard::class)->name('booking');
        Route::get('/bookings/{booking}/payment', BookingPayment::class)->name('booking.payment');
        Route::get('/bookings/{booking}/payment/return', BookingPaymentReturn::class)->name('booking.payment-return');
        Route::get('/bookings/{booking}/payment/failed', BookingPaymentFailed::class)->name('booking.payment-failed');
        Route::get('/bookings/{booking}/confirmation', BookingConfirmation::class)->name('booking.confirmation');
        Route::get('/bookings/{booking}/change-request', BookingChangeRequest::class)->name('booking.change-request');
    });

// ── Admin ─────────────────────────────────────────────────────────────────────
Route::middleware(['web', 'auth', 'set-company:hostels'])
    ->prefix('hostels/admin')
    ->name('hostels.')
    ->group(function () {
        Route::get('/', HostelList::class)->name('index');
        Route::get('/{hostel:slug}', Dashboard::class)->name('dashboard');
        Route::get('/{hostel:slug}/rooms', RoomList::class)->name('rooms.index');
        Route::get('/{hostel:slug}/rooms/{room:slug}/beds', BedList::class)->name('beds.index');
        Route::get('/{hostel:slug}/whatsapp-groups', HostelWhatsAppGroupList::class)->name('whatsapp-groups.index');
        Route::get('/{hostel:slug}/whatsapp-groups/{group}', WhatsAppGroupMessages::class)->name('whatsapp-groups.show');
        Route::get('/{hostel:slug}/hostel-occupants', HostelOccupantList::class)->name('hostel-occupants.index');
        Route::get('/{hostel:slug}/bookings', BookingList::class)->name('bookings.index');
        Route::get('/{hostel:slug}/bookings/create', AdminBookingsCreate::class)->name('bookings.create');
        Route::get('/{hostel:slug}/bookings/{booking}', AdminBookingsShow::class)->name('bookings.show');
        Route::get('/{hostel:slug}/maintenance', AdminMaintenanceIndex::class)->name('maintenance.index');
        Route::get('/{hostel:slug}/incidents', AdminIncidentsIndex::class)->name('incidents.index');
        Route::get('/{hostel:slug}/visitors', AdminVisitorsIndex::class)->name('visitors.index');
        Route::get('/{hostel:slug}/reports', AdminReportsIndex::class)->name('reports.index');
        Route::get('/{hostel:slug}/hostel-charges', HostelChargeList::class)->name('hostel-charges.index');
        Route::get('/{hostel:slug}/change-requests', BookingChangeRequests::class)->name('change-requests.index');
        Route::get('/{hostel:slug}/booking-approvals', BookingApproval::class)->name('booking-approvals.index');
        Route::get('/{hostel:slug}/check-in', CheckIn::class)->name('check-in');
        Route::get('/{hostel:slug}/check-out', CheckOut::class)->name('check-out');
        Route::get('/{hostel:slug}/deposit-collection', DepositCollection::class)->name('deposit-collection');
    });
