<?php

namespace Modules\Hostels\Http\Livewire\Admin;

use Livewire\Component;
use Modules\Hostels\Models\Booking;

class BookingList extends Component
{
    public function render()
    {
        return view('hostels::livewire.admin.booking-list', [
            'bookings' => Booking::where('hostel_id', request()->route('hostel'))->paginate(),
        ])->layout('layouts.app');
    }
}
