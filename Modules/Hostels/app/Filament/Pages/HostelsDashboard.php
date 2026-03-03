<?php

namespace Modules\Hostels\Filament\Pages;

use Filament\Pages\Page;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\Incident;
use Modules\Hostels\Models\MaintenanceRequest;
use Modules\Hostels\Models\Room;

class HostelsDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Dashboard';

    protected string $view = 'hostels::filament.pages.hostels-dashboard';

    public array $stats  = [];
    public array $alerts = [];

    public function mount(): void
    {
        $companyId = auth()->user()?->current_company_id;

        $this->stats  = $this->buildStats($companyId);
        $this->alerts = $this->buildAlerts($companyId);
    }

    protected function buildStats(?int $companyId): array
    {
        $hostelQ  = Hostel::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $roomQ    = Room::query()->when($companyId, fn ($q) => $q->whereHas('hostel', fn ($h) => $h->where('company_id', $companyId)));
        $bookingQ = Booking::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $mReqQ    = MaintenanceRequest::query()->when($companyId, fn ($q) => $q->whereHas('hostel', fn ($h) => $h->where('company_id', $companyId)));
        $incidentQ = Incident::query()->when($companyId, fn ($q) => $q->whereHas('hostel', fn ($h) => $h->where('company_id', $companyId)));
        $occupantQ = HostelOccupant::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        $totalRooms     = (clone $roomQ)->count();
        $availableRooms = (clone $roomQ)->where('status', 'available')->count();
        $fullRooms      = (clone $roomQ)->where('status', 'full')->count();

        $roomsMaxOccupancy     = (clone $roomQ)->sum('max_occupancy');
        $roomsCurrentOccupancy = (clone $roomQ)->sum('current_occupancy');

        return [
            'total_hostels'         => (clone $hostelQ)->where('status', 'active')->count(),
            'total_rooms'           => $totalRooms,
            'available_rooms'       => $availableRooms,
            'maintenance_rooms'     => (clone $roomQ)->where('status', 'maintenance')->count(),
            'occupancy_rate'        => $roomsMaxOccupancy > 0
                ? round(($roomsCurrentOccupancy / $roomsMaxOccupancy) * 100, 1)
                : null,
            'current_occupancy'     => $roomsCurrentOccupancy,
            'max_occupancy'         => $roomsMaxOccupancy,
            'active_bookings'       => (clone $bookingQ)->where('status', 'checked_in')->count(),
            'checkins_today'        => (clone $bookingQ)->whereDate('check_in_date', today())->whereIn('status', ['confirmed', 'checked_in'])->count(),
            'checkouts_today'       => (clone $bookingQ)->whereDate('check_out_date', today())->where('status', 'checked_in')->count(),
            'open_maintenance'      => (clone $mReqQ)->whereIn('status', ['open', 'in_progress'])->count(),
            'open_incidents'        => (clone $incidentQ)->whereIn('status', ['open', 'escalated'])->count(),
            'total_occupants'       => (clone $occupantQ)->count(),
        ];
    }

    protected function buildAlerts(?int $companyId): array
    {
        $alerts = [];

        $criticalIncidents = Incident::query()
            ->when($companyId, fn ($q) => $q->whereHas('hostel', fn ($h) => $h->where('company_id', $companyId)))
            ->where('severity', 'critical')
            ->where('status', '!=', 'resolved')
            ->count();

        if ($criticalIncidents > 0) {
            $alerts[] = [
                'type'    => 'danger',
                'message' => "{$criticalIncidents} unresolved critical incident(s) require immediate attention.",
            ];
        }

        $urgentMaintenance = MaintenanceRequest::query()
            ->when($companyId, fn ($q) => $q->whereHas('hostel', fn ($h) => $h->where('company_id', $companyId)))
            ->where('priority', 'urgent')
            ->where('status', 'open')
            ->count();

        if ($urgentMaintenance > 0) {
            $alerts[] = [
                'type'    => 'warning',
                'message' => "{$urgentMaintenance} urgent maintenance request(s) are open and unassigned.",
            ];
        }

        $checkoutsToday = Booking::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->whereDate('check_out_date', today())
            ->where('status', 'checked_in')
            ->count();

        if ($checkoutsToday > 0) {
            $alerts[] = [
                'type'    => 'info',
                'message' => "{$checkoutsToday} guest(s) are due to check out today.",
            ];
        }

        return $alerts;
    }
}
