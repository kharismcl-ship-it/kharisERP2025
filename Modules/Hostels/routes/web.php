<?php

use Illuminate\Support\Facades\Route;
use Modules\Hostels\Http\Livewire\Admin\BookingApproval;
use Modules\Hostels\Http\Livewire\BedList;
use Modules\Hostels\Http\Livewire\BookingChangeRequests;
use Modules\Hostels\Http\Livewire\BookingList;
use Modules\Hostels\Http\Livewire\Bookings\Create;
use Modules\Hostels\Http\Livewire\Bookings\Show;
use Modules\Hostels\Http\Livewire\Dashboard;
use Modules\Hostels\Http\Livewire\HostelChargeList;
use Modules\Hostels\Http\Livewire\HostelList;
use Modules\Hostels\Http\Livewire\HostelOccupantList;
use Modules\Hostels\Http\Livewire\HostelWhatsAppGroupList;
use Modules\Hostels\Http\Livewire\Incidents\Index as IncidentsIndex;
use Modules\Hostels\Http\Livewire\Maintenance\Index as MaintenanceIndex;
use Modules\Hostels\Http\Livewire\Public\BookingChangeRequest;
// Public routes
use Modules\Hostels\Http\Livewire\Public\BookingConfirmation;
use Modules\Hostels\Http\Livewire\Public\BookingPayment;
use Modules\Hostels\Http\Livewire\Public\BookingPaymentFailed;
use Modules\Hostels\Http\Livewire\Public\BookingPaymentReturn;
use Modules\Hostels\Http\Livewire\Public\BookingWizard;
use Modules\Hostels\Http\Livewire\Public\Index as PublicIndex;
use Modules\Hostels\Http\Livewire\Public\Show as PublicShow;
use Modules\Hostels\Http\Livewire\Reports\Index as ReportsIndex;
use Modules\Hostels\Http\Livewire\RoomList;
use Modules\Hostels\Http\Livewire\Visitors\Index as VisitorsIndex;
use Modules\Hostels\Http\Livewire\WhatsAppGroupMessages;

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
        Route::get('/{hostel:slug}/bookings/create', Create::class)->name('bookings.create');
        Route::get('/{hostel:slug}/bookings/{booking}', Show::class)->name('bookings.show');
        Route::get('/{hostel:slug}/maintenance', MaintenanceIndex::class)->name('maintenance.index');
        Route::get('/{hostel:slug}/incidents', IncidentsIndex::class)->name('incidents.index');
        Route::get('/{hostel:slug}/visitors', VisitorsIndex::class)->name('visitors.index');
        Route::get('/{hostel:slug}/reports', ReportsIndex::class)->name('reports.index');
        Route::get('/{hostel:slug}/hostel-charges', HostelChargeList::class)->name('hostel-charges.index');
        Route::get('/{hostel:slug}/change-requests', BookingChangeRequests::class)->name('change-requests.index');
        Route::get('/{hostel:slug}/booking-approvals', BookingApproval::class)->name('booking-approvals.index');
    });
