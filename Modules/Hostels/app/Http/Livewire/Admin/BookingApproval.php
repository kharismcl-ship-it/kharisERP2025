<?php

namespace Modules\Hostels\Http\Livewire\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Notifications\BookingApprovedNotification;
use Modules\Hostels\Notifications\BookingRejectedNotification;

class BookingApproval extends Component
{
    use AuthorizesRequests, WithPagination;

    public $search = '';

    public $statusFilter = 'pending_approval';

    public $perPage = 10;

    public $selectedBooking = null;

    public $showApprovalModal = false;

    public $rejectionReason = '';

    protected $queryString = ['search', 'statusFilter', 'perPage'];

    public function mount()
    {
        $this->authorize('viewAny', Booking::class);
    }

    public function approveBooking($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $this->authorize('approve', $booking);

        try {
            $result = $booking->approveBooking();

            $this->dispatchBrowserEvent('notify', [
                'type' => 'success',
                'message' => $result['message'],
            ]);

            // Send notification to guest
            if ($booking->guest_email) {
                Notification::route('mail', $booking->guest_email)
                    ->notify(new BookingApprovedNotification($booking));
            }

        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

        $this->showApprovalModal = false;
    }

    public function rejectBooking()
    {
        $this->validate(['rejectionReason' => 'required|string|min:10|max:500']);

        $booking = Booking::findOrFail($this->selectedBooking);
        $this->authorize('approve', $booking);

        try {
            $result = $booking->rejectBooking($this->rejectionReason);

            $this->dispatchBrowserEvent('notify', [
                'type' => 'success',
                'message' => $result['message'],
            ]);

            // Send notification to guest
            if ($booking->guest_email) {
                Notification::route('mail', $booking->guest_email)
                    ->notify(new BookingRejectedNotification($booking, $this->rejectionReason));
            }

        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

        $this->showApprovalModal = false;
        $this->rejectionReason = '';
        $this->selectedBooking = null;
    }

    public function openApprovalModal($bookingId, $action)
    {
        $this->selectedBooking = $bookingId;
        $this->showApprovalModal = true;

        if ($action === 'reject') {
            $this->dispatchBrowserEvent('open-rejection-modal');
        }
    }

    public function getBookingsProperty()
    {
        return Booking::query()
            ->with(['hostel', 'bed', 'bed.room'])
            ->where('status', $this->statusFilter)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('booking_reference', 'like', "%{$this->search}%")
                        ->orWhere('guest_first_name', 'like', "%{$this->search}%")
                        ->orWhere('guest_last_name', 'like', "%{$this->search}%")
                        ->orWhere('guest_email', 'like', "%{$this->search}%")
                        ->orWhere('guest_phone', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('hostels::livewire.admin.booking-approval', [
            'bookings' => $this->bookings,
            'statusOptions' => [
                'pending_approval' => 'Pending Approval',
                'confirmed' => 'Confirmed',
                'rejected' => 'Rejected',
            ],
        ]);
    }
}
