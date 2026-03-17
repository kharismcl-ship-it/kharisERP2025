<?php

namespace Modules\Fleet\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Fleet\Models\FuelLog;
use Modules\Fleet\Models\MaintenanceRecord;
use Modules\Fleet\Models\TripLog;
use Modules\Fleet\Models\Vehicle;
use Modules\Fleet\Models\VehicleDocument;

class FleetDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|\UnitEnum|null $navigationGroup = 'Fleet';

    protected static ?int $navigationSort = 1;

    use HasPageShield;

    protected static ?string $navigationLabel = 'Dashboard';

    protected string $view = 'fleet::filament.pages.fleet-dashboard';

    public array $stats = [];

    public function mount(): void
    {
        $this->stats = $this->buildStats();
    }

    protected function buildStats(): array
    {
        $companyId = auth()->user()?->current_company_id;

        $vehicleQ = Vehicle::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        // Fleet composition
        $totalVehicles   = (clone $vehicleQ)->count();
        $activeVehicles  = (clone $vehicleQ)->where('status', 'active')->count();
        $inMaintenance   = (clone $vehicleQ)->where('status', 'under_maintenance')->count();
        $retiredVehicles = (clone $vehicleQ)->where('status', 'retired')->count();

        // Fuel spend
        $fuelQ = FuelLog::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $fuelSpendMtd = (clone $fuelQ)
            ->whereMonth('fill_date', now()->month)
            ->whereYear('fill_date', now()->year)
            ->sum('total_cost');
        $fuelSpendYtd = (clone $fuelQ)
            ->whereYear('fill_date', now()->year)
            ->sum('total_cost');

        // Maintenance spend
        $maintQ = MaintenanceRecord::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $maintSpendYtd = (clone $maintQ)
            ->where('status', 'completed')
            ->whereYear('service_date', now()->year)
            ->sum('cost');
        $scheduledMaintenance = (clone $maintQ)
            ->where('status', 'scheduled')
            ->count();

        // Trips
        $tripQ = TripLog::query()->when($companyId, fn ($q) => $q->where('company_id', $companyId));
        $activeTrips  = (clone $tripQ)->where('status', 'in_progress')->count();
        $tripsMtd     = (clone $tripQ)
            ->whereMonth('trip_date', now()->month)
            ->whereYear('trip_date', now()->year)
            ->count();
        $distanceMtd  = (clone $tripQ)
            ->where('status', 'completed')
            ->whereMonth('trip_date', now()->month)
            ->whereYear('trip_date', now()->year)
            ->sum('distance_km');

        // Documents expiring within 30 days
        $expiringDocuments = VehicleDocument::query()
            ->when($companyId, fn ($q) => $q->whereHas('vehicle', fn ($vq) => $vq->where('company_id', $companyId)))
            ->whereBetween('expiry_date', [now(), now()->addDays(30)])
            ->count();

        // Top 5 fuel-consuming vehicles this year
        $topFuelVehicles = Vehicle::query()
            ->when($companyId, fn ($q) => $q->where('vehicles.company_id', $companyId))
            ->join('fuel_logs', 'vehicles.id', '=', 'fuel_logs.vehicle_id')
            ->whereYear('fuel_logs.fill_date', now()->year)
            ->selectRaw('vehicles.name, vehicles.plate, SUM(fuel_logs.total_cost) as total_fuel_cost')
            ->groupBy('vehicles.id', 'vehicles.name', 'vehicles.plate')
            ->orderByDesc('total_fuel_cost')
            ->take(5)
            ->get();

        return compact(
            'totalVehicles',
            'activeVehicles',
            'inMaintenance',
            'retiredVehicles',
            'fuelSpendMtd',
            'fuelSpendYtd',
            'maintSpendYtd',
            'scheduledMaintenance',
            'activeTrips',
            'tripsMtd',
            'distanceMtd',
            'expiringDocuments',
            'topFuelVehicles'
        );
    }
}
