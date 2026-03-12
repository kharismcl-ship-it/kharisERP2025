<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant;

use Livewire\Component;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Deposit;
use Modules\Hostels\Models\Incident;
use Modules\Hostels\Models\MaintenanceRequest;
use Modules\Hostels\Models\VisitorLog;

class Dashboard extends Component
{
    public function getStatsProperty(): array
    {
        $id = auth('hostel_occupant')->user()->hostel_occupant_id;

        return [
            'active_bookings'  => Booking::where('hostel_occupant_id', $id)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->count(),
            'pending_bookings' => Booking::where('hostel_occupant_id', $id)
                ->where('status', 'pending')
                ->count(),
            'open_maintenance' => MaintenanceRequest::where('reported_by_hostel_occupant_id', $id)
                ->whereIn('status', ['open', 'in_progress'])
                ->count(),
            'open_incidents'   => Incident::where('hostel_occupant_id', $id)
                ->whereNotIn('status', ['resolved', 'closed'])
                ->count(),
        ];
    }

    public function render()
    {
        $user     = auth('hostel_occupant')->user();
        $occupant = $user->hostelOccupant()->with('hostel')->first();
        $id       = $occupant->id;

        // Active stay: confirmed or checked_in, most recent check-in date
        $activeBooking = Booking::where('hostel_occupant_id', $id)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->with(['hostel', 'room', 'bed'])
            ->orderByDesc('check_in_date')
            ->first();

        $deposit = $activeBooking
            ? Deposit::where('booking_id', $activeBooking->id)->first()
            : null;

        $recentBookings = Booking::where('hostel_occupant_id', $id)
            ->with(['room', 'bed'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentMaintenance = MaintenanceRequest::where('reported_by_hostel_occupant_id', $id)
            ->with('room')
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        $recentIncidents = Incident::where('hostel_occupant_id', $id)
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        $recentVisitors = VisitorLog::where('hostel_occupant_id', $id)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return view('hostels::livewire.hostel-occupant.dashboard', [
            'occupant'          => $occupant,
            'activeBooking'     => $activeBooking,
            'deposit'           => $deposit,
            'stats'             => $this->stats,
            'recentBookings'    => $recentBookings,
            'recentMaintenance' => $recentMaintenance,
            'recentIncidents'   => $recentIncidents,
            'recentVisitors'    => $recentVisitors,
        ])->layout('hostels::layouts.occupant');
    }
}
