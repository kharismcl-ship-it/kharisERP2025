<?php

namespace Modules\Hostels\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\BookingChangeRequest as ChangeRequest;
use Modules\Hostels\Models\Room;

class BookingChangeRequests extends Component
{
    use WithPagination;

    public $statusFilter = '';

    public $search = '';

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'statusFilter' => 'nullable|in:pending,approved,rejected',
        'search' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        //
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function approveRequest(ChangeRequest $changeRequest)
    {
        // Check if the requested bed/room is still available
        if ($changeRequest->requested_bed_id) {
            $bed = Bed::find($changeRequest->requested_bed_id);
            if (! $bed || $bed->status !== 'available') {
                session()->flash('error', 'The requested bed is no longer available.');

                return;
            }
        }

        if ($changeRequest->requested_room_id) {
            $room = Room::find($changeRequest->requested_room_id);
            if (! $room || $room->status !== 'available') {
                session()->flash('error', 'The requested room is no longer available.');

                return;
            }
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Update the change request
            $changeRequest->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Get the booking
            $booking = $changeRequest->booking;

            // Release the old bed if it exists
            if ($booking->bed_id) {
                $oldBed = Bed::find($booking->bed_id);
                if ($oldBed) {
                    $oldBed->update(['status' => 'available']);
                }
            }

            // Update the booking with new room/bed
            $booking->update([
                'room_id' => $changeRequest->requested_room_id,
                'bed_id' => $changeRequest->requested_bed_id,
            ]);

            // Reserve the new bed if it exists
            if ($changeRequest->requested_bed_id) {
                $newBed = Bed::find($changeRequest->requested_bed_id);
                if ($newBed) {
                    $newBed->update(['status' => 'occupied']);
                }
            }

            // Commit the transaction
            DB::commit();

            session()->flash('message', 'Change request approved successfully.');
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollback();

            Log::error('Error approving booking change request: '.$e->getMessage());
            session()->flash('error', 'An error occurred while approving the change request. Please try again.');
        }
    }

    public function rejectRequest(ChangeRequest $changeRequest, $reason = '')
    {
        $changeRequest->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'notes' => $reason,
        ]);

        session()->flash('message', 'Change request rejected.');
    }

    public function getChangeRequestsProperty()
    {
        return ChangeRequest::with(['booking', 'booking.hostelOccupant', 'requestedRoom', 'requestedBed', 'approvedBy'])
            ->when($this->statusFilter, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($this->search, function ($query, $search) {
                return $query->whereHas('booking', function ($q) use ($search) {
                    $q->where('booking_reference', 'like', '%'.$search.'%')
                        ->orWhereHas('hostelOccupant', function ($hostelOccupantQuery) use ($search) {
                            $hostelOccupantQuery->where('first_name', 'like', '%'.$search.'%')
                                ->orWhere('last_name', 'like', '%'.$search.'%')
                                ->orWhere('email', 'like', '%'.$search.'%');
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        return view('hostels::livewire.booking-change-requests');
    }
}
