<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Bookings;

use Livewire\Component;
use Modules\Hostels\Models\Booking;

class Receipt extends Component
{
    public Booking $booking;

    public function mount(Booking $booking): void
    {
        if ($booking->hostel_occupant_id !== auth('hostel_occupant')->user()->hostel_occupant_id) {
            abort(403);
        }

        $this->booking = $booking->load(['hostel', 'room', 'bed', 'charges.feeType']);
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.bookings.receipt')
            ->layout('hostels::layouts.receipt');
    }
}
