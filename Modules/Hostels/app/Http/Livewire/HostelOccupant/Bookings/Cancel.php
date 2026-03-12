<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Bookings;

use Livewire\Component;
use Modules\Hostels\Models\Booking;

class Cancel extends Component
{
    public Booking $booking;

    public float $estimatedRefund = 0;

    public bool $cancellationAllowed = true;

    public string $policyName = '';

    public bool $confirmed = false;

    public function mount(Booking $booking): void
    {
        if ($booking->hostel_occupant_id !== auth('hostel_occupant')->user()->hostel_occupant_id) {
            abort(403);
        }

        if (! in_array($booking->status, ['pending', 'awaiting_payment', 'confirmed'])) {
            abort(403, 'This booking cannot be cancelled.');
        }

        $this->booking = $booking;

        try {
            $policy = $booking->getApplicableCancellationPolicy();
            $this->policyName = $policy->name;
            $this->cancellationAllowed = $policy->isCancellationAllowed($booking->check_in_date);

            if ($this->cancellationAllowed) {
                $this->estimatedRefund = $policy->calculateRefundAmount(
                    $booking->getTotalPaidAmount(),
                    $booking->getTotalPaidAmount()
                );
            }
        } catch (\Exception $e) {
            $this->cancellationAllowed = false;
        }
    }

    public function cancel(): void
    {
        if (! $this->confirmed) {
            $this->dispatch('notify', type: 'error', message: 'Please confirm cancellation first.');
            return;
        }

        try {
            $result = $this->booking->cancelBooking();

            session()->flash('success', 'Booking cancelled. '
                . ($result['refund_amount'] > 0
                    ? 'Refund of ' . number_format($result['refund_amount'], 2) . ' will be processed.'
                    : 'No refund applicable per cancellation policy.')
            );

            $this->redirect(route('hostel_occupant.bookings.index'));
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.bookings.cancel')
            ->layout('hostels::layouts.occupant');
    }
}
