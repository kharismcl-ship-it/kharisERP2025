<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Bookings;

use Livewire\Component;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Deposit;

class Show extends Component
{
    public Booking $booking;

    public string $activeTab = 'details';

    public function mount(Booking $booking): void
    {
        if ($booking->hostel_occupant_id !== auth('hostel_occupant')->user()->hostel_occupant_id) {
            abort(403);
        }

        $this->booking = $booking->load(['hostel', 'room', 'bed']);
    }

    public function getDepositProperty(): ?Deposit
    {
        return Deposit::where('booking_id', $this->booking->id)->first();
    }

    public function getChargesProperty()
    {
        return $this->booking->charges()->with('feeType')->get();
    }

    public function getPaymentsProperty()
    {
        return $this->booking->payIntents()->latest()->get();
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.bookings.show', [
            'deposit'  => $this->deposit,
            'charges'  => $this->charges,
            'payments' => $this->payments,
        ])->layout('hostels::layouts.occupant');
    }
}
