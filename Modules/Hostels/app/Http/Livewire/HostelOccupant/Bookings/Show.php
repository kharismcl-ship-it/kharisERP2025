<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Bookings;

use Livewire\Component;
use Modules\Hostels\Models\Booking;

class Show extends Component
{
    public function __invoke()
    {
        return $this->render();
    }

    public Booking $booking;

    public function mount(Booking $booking)
    {
        $this->booking = $booking;

        // Ensure hostel occupant can only view their own bookings
        if ($booking->hostel_occupant_id !== auth('hostel_occupant')->user()->hostel_occupant_id) {
            abort(403);
        }
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.bookings.show')
            ->layout('hostels::layouts.app');
    }
}
