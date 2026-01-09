<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Bookings;

use Livewire\Component;
use Modules\Hostels\Models\Booking;

class Index extends Component
{
    public function __invoke()
    {
        return $this->render();
    }

    public function render()
    {
        $hostelOccupantId = auth('hostel_occupant')->user()->hostel_occupant_id;

        $bookings = Booking::where('hostel_occupant_id', $hostelOccupantId)
            ->with(['hostel', 'room'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hostels::livewire.hostel-occupant.bookings.index', [
            'bookings' => $bookings,
        ])->layout('hostels::layouts.app');
    }
}
