<?php

namespace Modules\Hostels\Http\Livewire\Admin;

use Livewire\Component;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Hostel;

class CheckIn extends Component
{
    public Hostel $hostel;

    public string $search = '';

    public ?Booking $booking = null;

    public string $signature = '';

    public bool $showConfirm = false;

    public function mount(Hostel $hostel): void
    {
        $this->hostel = $hostel;
    }

    public function searchBooking(): void
    {
        $this->booking = null;
        $this->showConfirm = false;

        if (strlen($this->search) < 3) {
            return;
        }

        $this->booking = Booking::with(['hostel', 'room', 'bed'])
            ->where('hostel_id', $this->hostel->id)
            ->whereIn('status', ['confirmed', 'awaiting_payment'])
            ->where(function ($q) {
                $q->where('booking_reference', 'like', "%{$this->search}%")
                    ->orWhere('guest_first_name', 'like', "%{$this->search}%")
                    ->orWhere('guest_last_name', 'like', "%{$this->search}%")
                    ->orWhere('guest_phone', 'like', "%{$this->search}%");
            })
            ->first();
    }

    public function confirmCheckIn(): void
    {
        if (! $this->booking) {
            return;
        }

        try {
            if ($this->signature) {
                $this->booking->update(['guest_check_in_signature' => $this->signature]);
            }

            $this->booking->checkIn();

            session()->flash('success', "Guest {$this->booking->guest_full_name} checked in successfully. Bed marked occupied.");

            $this->booking = null;
            $this->search = '';
            $this->signature = '';
            $this->showConfirm = false;

            $this->dispatch('notify', type: 'success', message: 'Check-in completed successfully.');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('hostels::livewire.admin.check-in')
            ->layout('layouts.app');
    }
}
