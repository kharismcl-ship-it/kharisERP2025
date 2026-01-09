<?php

namespace Modules\Hostels\Http\Livewire\Reports;

use Livewire\Component;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\Incident;
use Modules\Hostels\Models\MaintenanceRequest;
use Modules\Hostels\Models\Room;

class Index extends Component
{
    public Hostel $hostel;

    public $activeTab = 'occupancy';

    public $dateRange = 'this_month';

    public function mount(Hostel $hostel)
    {
        $this->hostel = $hostel;
    }

    public function getOccupancyStatsProperty()
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
        ];
    }

    public function getBookingStatsProperty()
    {
        $query = Booking::where('hostel_id', $this->hostel->id);

        // Apply date range filter
        switch ($this->dateRange) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'this_week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'this_year':
                $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
                break;
        }

        $totalBookings = $query->count();
        $confirmedBookings = $query->where('status', 'confirmed')->count();
        $checkedInBookings = $query->where('status', 'checked_in')->count();
        $checkedOutBookings = $query->where('status', 'checked_out')->count();

        return [
            'total' => $totalBookings,
            'confirmed' => $confirmedBookings,
            'checked_in' => $checkedInBookings,
            'checked_out' => $checkedOutBookings,
        ];
    }

    public function getMaintenanceStatsProperty()
    {
        $totalRequests = MaintenanceRequest::where('hostel_id', $this->hostel->id)->count();
        $openRequests = MaintenanceRequest::where('hostel_id', $this->hostel->id)
            ->where('status', 'open')
            ->count();
        $inProgressRequests = MaintenanceRequest::where('hostel_id', $this->hostel->id)
            ->where('status', 'in_progress')
            ->count();
        $completedRequests = MaintenanceRequest::where('hostel_id', $this->hostel->id)
            ->where('status', 'completed')
            ->count();

        return [
            'total' => $totalRequests,
            'open' => $openRequests,
            'in_progress' => $inProgressRequests,
            'completed' => $completedRequests,
        ];
    }

    public function getIncidentStatsProperty()
    {
        $totalIncidents = Incident::where('hostel_id', $this->hostel->id)->count();
        $openIncidents = Incident::where('hostel_id', $this->hostel->id)
            ->where('status', 'open')
            ->count();
        $resolvedIncidents = Incident::where('hostel_id', $this->hostel->id)
            ->where('status', 'resolved')
            ->count();
        $criticalIncidents = Incident::where('hostel_id', $this->hostel->id)
            ->where('severity', 'critical')
            ->count();

        return [
            'total' => $totalIncidents,
            'open' => $openIncidents,
            'resolved' => $resolvedIncidents,
            'critical' => $criticalIncidents,
        ];
    }

    public function getRoomTypeOccupancyProperty()
    {
        return Room::where('hostel_id', $this->hostel->id)
            ->selectRaw('room_type, COUNT(*) as total_rooms, SUM(current_occupancy) as occupied_beds, SUM(max_occupancy) as total_beds')
            ->groupBy('room_type')
            ->get();
    }

    public function render()
    {
        return view('hostels::livewire.reports.index')
            ->layout('layouts.app');
    }
}
