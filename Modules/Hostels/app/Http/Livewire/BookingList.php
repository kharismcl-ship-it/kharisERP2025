<?php

namespace Modules\Hostels\Http\Livewire;

use Livewire\Component;
use Modules\Hostels\Models\Booking;

class BookingList extends Component
{
    public function render()
    {
        return view('hostels::livewire.booking-list', [
            'bookings' => Booking::whereHas('tenant', function ($query) {
                $query->where('company_id', auth()->user()->currentCompanyId());
            })->paginate(),
        ])->layout('layouts.app');
    }
}
