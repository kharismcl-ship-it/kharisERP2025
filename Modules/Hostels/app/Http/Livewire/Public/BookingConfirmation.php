<?php

namespace Modules\Hostels\Http\Livewire\Public;

use Livewire\Component;
use Modules\Hostels\Models\Booking;

class BookingConfirmation extends Component
{
    public Booking $booking;

    public function mount(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function getOfflinePaymentMessageProperty()
    {
        return session()->get('offline_payment_success');
    }

    public function render()
    {
        return view('hostels::livewire.public.booking-confirmation')
            ->layout('hostels::layouts.public');
    }
}
