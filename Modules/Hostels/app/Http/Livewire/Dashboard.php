<?php

namespace Modules\Hostels\Http\Livewire;

use Livewire\Component;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\Incident;
use Modules\Hostels\Models\MaintenanceRequest;
use Modules\Hostels\Models\Room;

class Dashboard extends Component
{
    public Hostel $hostel;

    public function mount(Hostel $hostel)
    {
        $this->hostel = $hostel;
    }

    public function getStatsProperty()
    {
        $totalBeds = Bed::whereHas('room', function ($query) {
            $query->where('hostel_id', $this->hostel->id);
        })->count();

        $occupiedBeds = Bed::whereHas('room', function ($query) {
            $query->where('hostel_id', $this->hostel->id);
        })->where('status', 'occupied')->count();

        $availableBeds = $totalBeds - $occupiedBeds;

        $occupancyRate = $totalBeds > 0 ? ($occupiedBeds / $totalBeds) * 100 : 0;

        return [
            'total_beds' => $totalBeds,
            'occupied_beds' => $occupiedBeds,
            'available_beds' => $availableBeds,
            'occupancy_rate' => round($occupancyRate, 2),
            'total_rooms' => Room::where('hostel_id', $this->hostel->id)->count(),
            'active_bookings' => Booking::where('hostel_id', $this->hostel->id)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->count(),
            'total_hostel_occupants' => HostelOccupant::where('hostel_id', $this->hostel->id)
                ->where('status', 'active')
                ->count(),
            'open_maintenance' => MaintenanceRequest::where('hostel_id', $this->hostel->id)
                ->where('status', 'open')
                ->count(),
            'open_incidents' => Incident::where('hostel_id', $this->hostel->id)
                ->where('status', 'open')
                ->count(),
        ];
    }

    public function render()
    {
        return view('hostels::livewire.dashboard')
            ->layout('layouts.app');
    }
}
