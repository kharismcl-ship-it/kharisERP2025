<?php

namespace Modules\Hostels\Http\Livewire\Bookings;

use Livewire\Component;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\Room;

class Show extends Component
{
    public Hostel $hostel;

    public Booking $booking;

    public $showCheckInModal = false;

    public $showCheckOutModal = false;

    public $showRoomChangeModal = false;

    public $checkOutCharges = [];

    public $newRoomId;

    public $newBedId;

    protected $rules = [
        'checkOutCharges.*.description' => 'required|string|max:255',
        'checkOutCharges.*.amount' => 'required|numeric|min:0',
        'newRoomId' => 'required|exists:rooms,id',
        'newBedId' => 'nullable|exists:beds,id',
    ];

    public function mount(Hostel $hostel, Booking $booking)
    {
        $this->hostel = $hostel;
        $this->booking = $booking;
    }

    public function checkIn()
    {
        // Check if booking can be checked in
        if (! in_array($this->booking->status, ['confirmed', 'awaiting_payment'])) {
            $this->addError('checkin', 'Booking cannot be checked in at this stage.');

            return;
        }

        // Enforce payment-before-check-in policy if enabled
        if ($this->booking->hostel->require_payment_before_checkin && $this->booking->status === 'awaiting_payment') {
            $this->addError('checkin', 'Payment is required before check-in. This hostel requires payment confirmation before guests can check in.');

            return;
        }

        // Check if minimum payment requirement is met for deposits/partial payments
        if ($this->booking->hostel->require_deposit && $this->booking->deposit_paid < $this->booking->deposit_amount) {
            $this->addError('checkin', 'Deposit payment is required before check-in. Please ensure the deposit has been paid.');

            return;
        }

        // Update booking
        $this->booking->update([
            'actual_check_in_at' => now(),
            'status' => 'checked_in',
        ]);

        // Update bed status if assigned
        if ($this->booking->bed_id) {
            $this->booking->bed->update(['status' => 'occupied']);
        }

        // Update room occupancy
        $this->booking->room->increment('current_occupancy');

        $this->showCheckInModal = false;
        $this->dispatchBrowserEvent('notification', ['message' => 'Booking checked in successfully!']);
    }

    public function checkOut()
    {
        // Validate any additional charges
        $this->validate();

        // Add additional charges to booking
        foreach ($this->checkOutCharges as $charge) {
            if (! empty($charge['description']) && $charge['amount'] > 0) {
                $this->booking->charges()->create([
                    'description' => $charge['description'],
                    'quantity' => 1,
                    'unit_price' => $charge['amount'],
                    'amount' => $charge['amount'],
                ]);
            }
        }

        // Update booking
        $this->booking->update([
            'actual_check_out_at' => now(),
            'status' => 'checked_out',
        ]);

        // Update bed status if assigned
        if ($this->booking->bed_id) {
            $this->booking->bed->update(['status' => 'available']);
        }

        // Update room occupancy
        $this->booking->room->decrement('current_occupancy');

        // Update financials
        $additionalCharges = collect($this->checkOutCharges)->sum('amount');
        $newTotal = $this->booking->total_amount + $additionalCharges;
        $balance = $newTotal - $this->booking->amount_paid;

        // Handle deposit refund based on hostel's refund policy
        $depositRefundAmount = 0.0;
        if ($this->booking->hostel->require_deposit && $this->booking->deposit_paid > 0) {
            // Check if deposit should be refunded (full or partial based on policy)
            $depositRefundAmount = $this->calculateDepositRefund($this->booking->deposit_paid);

            if ($depositRefundAmount > 0) {
                // Apply deposit refund to balance
                $balance -= $depositRefundAmount;
                $this->booking->deposit_refunded = $depositRefundAmount;
            }
        }

        $this->booking->update([
            'total_amount' => $newTotal,
            'balance_amount' => $balance,
            'payment_status' => $balance <= 0 ? 'paid' : ($this->booking->amount_paid > 0 ? 'partially_paid' : 'unpaid'),
        ]);

        $this->showCheckOutModal = false;
        $this->dispatchBrowserEvent('notification', ['message' => 'Booking checked out successfully!']);
    }

    /**
     * Calculate deposit refund amount based on hostel's refund policy
     */
    protected function calculateDepositRefund(float $depositPaid): float
    {
        $hostel = $this->booking->hostel;

        // Default: full refund if no specific policy is defined
        if (empty($hostel->deposit_refund_policy)) {
            return $depositPaid;
        }

        // Check refund policy - this is a simple implementation
        // In a real system, you'd parse the policy text or have structured rules
        $policy = strtolower($hostel->deposit_refund_policy);

        // Sample policy parsing - this can be enhanced based on actual policy structure
        if (str_contains($policy, 'non-refundable')) {
            return 0.0;
        }

        if (str_contains($policy, '50%') || str_contains($policy, 'half')) {
            return $depositPaid * 0.5;
        }

        if (str_contains($policy, '75%')) {
            return $depositPaid * 0.75;
        }

        // Default to full refund
        return $depositPaid;
    }

    public function changeRoom()
    {
        $this->validate([
            'newRoomId' => 'required|exists:rooms,id',
            'newBedId' => 'nullable|exists:beds,id',
        ]);

        // Store old room/bed for reference
        $oldRoomId = $this->booking->room_id;
        $oldBedId = $this->booking->bed_id;

        // Update booking with new room/bed
        $this->booking->update([
            'room_id' => $this->newRoomId,
            'bed_id' => $this->newBedId,
        ]);

        // Update old bed status if it was assigned
        if ($oldBedId) {
            Bed::where('id', $oldBedId)->update(['status' => 'available']);
        }

        // Update new bed status if assigned
        if ($this->newBedId) {
            Bed::where('id', $this->newBedId)->update(['status' => 'occupied']);
        }

        // Update room occupancies
        Room::where('id', $oldRoomId)->decrement('current_occupancy');
        Room::where('id', $this->newRoomId)->increment('current_occupancy');

        $this->showRoomChangeModal = false;
        $this->dispatchBrowserEvent('notification', ['message' => 'Room/Bed changed successfully!']);
    }

    public function addCheckOutCharge()
    {
        $this->checkOutCharges[] = [
            'description' => '',
            'amount' => 0,
        ];
    }

    public function removeCheckOutCharge($index)
    {
        unset($this->checkOutCharges[$index]);
        $this->checkOutCharges = array_values($this->checkOutCharges);
    }

    public function getCanCheckInProperty()
    {
        return in_array($this->booking->status, ['confirmed', 'awaiting_payment']);
    }

    public function getCanCheckOutProperty()
    {
        return $this->booking->status === 'checked_in';
    }

    public function getAvailableRoomsProperty()
    {
        return Room::where('hostel_id', $this->hostel->id)
            ->where('status', 'available')
            ->orWhere('status', 'partially_occupied')
            ->get();
    }

    public function getBedsForRoomProperty()
    {
        if (! $this->newRoomId) {
            return collect();
        }

        return Bed::where('room_id', $this->newRoomId)
            ->where('status', 'available')
            ->get();
    }

    public function render()
    {
        return view('hostels::livewire.bookings.show')
            ->layout('layouts.app');
    }
}
