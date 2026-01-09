<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant;

use Livewire\Component;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\MaintenanceRequest;

class Dashboard extends Component
{
    public function __invoke()
    {
        return $this->render();
    }

    public function getStatsProperty()
    {
        $hostelOccupantId = auth('hostel_occupant')->user()->hostel_occupant_id;

        return [
            'active_bookings' => Booking::where('hostel_occupant_id', $hostelOccupantId)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->count(),
            'pending_bookings' => Booking::where('hostel_occupant_id', $hostelOccupantId)
                ->where('status', 'pending')
                ->count(),
            'open_maintenance' => MaintenanceRequest::where('reported_by_hostel_occupant_id', $hostelOccupantId)
                ->where('status', 'open')
                ->count(),
        ];
    }

    public function render()
    {
        $hostelOccupantId = auth('hostel_occupant')->user()->hostel_occupant_id;

        $bookings = Booking::where('hostel_occupant_id', $hostelOccupantId)
            ->with(['hostel', 'room'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('hostels::livewire.hostel-occupant.dashboard', [
            'bookings' => $bookings,
        ])->layout('hostels::layouts.app');
    }
}
