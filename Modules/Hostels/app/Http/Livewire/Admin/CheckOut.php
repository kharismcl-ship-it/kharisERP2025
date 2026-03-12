<?php

namespace Modules\Hostels\Http\Livewire\Admin;

use Livewire\Component;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Deposit;
use Modules\Hostels\Models\Hostel;

class CheckOut extends Component
{
    public Hostel $hostel;

    public string $search = '';

    public ?Booking $booking = null;

    public bool $showConfirm = false;

    public float $refundAmount = 0;

    public string $deductionReason = '';

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

        $booking = Booking::with(['hostel', 'room', 'bed'])
            ->where('hostel_id', $this->hostel->id)
            ->where('status', 'checked_in')
            ->where(function ($q) {
                $q->where('booking_reference', 'like', "%{$this->search}%")
                    ->orWhere('guest_first_name', 'like', "%{$this->search}%")
                    ->orWhere('guest_last_name', 'like', "%{$this->search}%")
                    ->orWhere('guest_phone', 'like', "%{$this->search}%");
            })
            ->first();

        if ($booking) {
            $this->booking = $booking;
            $this->refundAmount = $booking->deposit_paid - $booking->deposit_refunded;
        }
    }

    public function getDepositProperty(): ?Deposit
    {
        if (! $this->booking) {
            return null;
        }

        return Deposit::where('booking_id', $this->booking->id)->first();
    }

    public function confirmCheckOut(): void
    {
        if (! $this->booking) {
            return;
        }

        try {
            $this->booking->update([
                'status'              => 'checked_out',
                'actual_check_out_at' => now(),
            ]);

            if ($this->booking->bed) {
                $this->booking->bed->update(['status' => 'available']);
            }

            // Process deposit refund if applicable
            $deposit = $this->deposit;
            if ($deposit && $deposit->canBeRefunded() && $this->refundAmount > 0) {
                $deductions = $deposit->amount - $this->refundAmount;
                $deductionReason = $this->deductionReason ?: null;
                $deposit->processRefund($this->refundAmount, $deductions, $deductionReason);
            }

            session()->flash('success', "Guest {$this->booking->guest_full_name} checked out. Bed released.");

            $this->dispatch('notify', type: 'success', message: 'Check-out completed successfully.');

            $this->booking = null;
            $this->search = '';
            $this->showConfirm = false;
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('hostels::livewire.admin.check-out')
            ->layout('layouts.app');
    }
}
