<?php

namespace Modules\Fleet\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Fleet\Models\FuelLog;
use Modules\Fleet\Models\MaintenanceRecord;
use Modules\Fleet\Models\Vehicle;

class FleetService
{
    /**
     * Record a fuel fill-up and update vehicle mileage.
     */
    public function recordFuelFillUp(Vehicle $vehicle, array $data): FuelLog
    {
        $log = FuelLog::create(array_merge($data, [
            'vehicle_id' => $vehicle->id,
            'company_id' => $vehicle->company_id,
        ]));

        // Update vehicle's current mileage if the fill mileage is higher
        if ($log->mileage_at_fill && $log->mileage_at_fill > $vehicle->current_mileage) {
            $vehicle->update(['current_mileage' => $log->mileage_at_fill]);
        }

        // Hook into Finance if available
        $this->recordFuelExpenseInFinance($vehicle, $log);

        return $log;
    }

    /**
     * Record a maintenance job and update vehicle status.
     */
    public function recordMaintenance(Vehicle $vehicle, array $data): MaintenanceRecord
    {
        $record = MaintenanceRecord::create(array_merge($data, [
            'vehicle_id' => $vehicle->id,
            'company_id' => $vehicle->company_id,
        ]));

        // Update vehicle mileage if service mileage is higher
        if ($record->mileage_at_service && $record->mileage_at_service > $vehicle->current_mileage) {
            $vehicle->update(['current_mileage' => $record->mileage_at_service]);
        }

        // Mark vehicle under_maintenance for in_progress jobs
        if ($record->status === 'in_progress') {
            $vehicle->update(['status' => 'under_maintenance']);
        }

        // Hook into Finance if available
        $this->recordMaintenanceExpenseInFinance($vehicle, $record);

        return $record;
    }

    /**
     * Mark a maintenance record as completed and restore vehicle to active.
     */
    public function completeMaintenance(MaintenanceRecord $record): void
    {
        $record->update(['status' => 'completed']);

        if ($record->vehicle && $record->vehicle->status === 'under_maintenance') {
            $record->vehicle->update(['status' => 'active']);
        }
    }

    /**
     * Calculate total fuel cost for a vehicle within a date range.
     */
    public function totalFuelCost(Vehicle $vehicle, string $from, string $to): float
    {
        return (float) $vehicle->fuelLogs()
            ->whereBetween('fill_date', [$from, $to])
            ->sum('total_cost');
    }

    /**
     * Calculate total maintenance cost for a vehicle within a date range.
     */
    public function totalMaintenanceCost(Vehicle $vehicle, string $from, string $to): float
    {
        return (float) $vehicle->maintenanceRecords()
            ->whereBetween('service_date', [$from, $to])
            ->sum('cost');
    }

    /**
     * Total distance driven in a date range.
     */
    public function totalDistance(Vehicle $vehicle, string $from, string $to): float
    {
        return (float) $vehicle->tripLogs()
            ->whereBetween('trip_date', [$from, $to])
            ->where('status', 'completed')
            ->sum('distance_km');
    }

    /**
     * Calculate fuel efficiency (L/100km) for a vehicle using consecutive fill-up mileage gaps.
     * Returns null if there are fewer than 2 fills with mileage data.
     */
    public function fuelEfficiency(Vehicle $vehicle): ?float
    {
        $fills = FuelLog::where('vehicle_id', $vehicle->id)
            ->whereNotNull('mileage_at_fill')
            ->orderBy('fill_date')
            ->orderBy('id')
            ->get(['litres', 'mileage_at_fill']);

        if ($fills->count() < 2) {
            return null;
        }

        $totalLitres   = 0.0;
        $totalDistance = 0.0;

        for ($i = 1; $i < $fills->count(); $i++) {
            $distance = (float) $fills[$i]->mileage_at_fill - (float) $fills[$i - 1]->mileage_at_fill;
            if ($distance > 0) {
                $totalLitres   += (float) $fills[$i]->litres;
                $totalDistance += $distance;
            }
        }

        if ($totalDistance <= 0) {
            return null;
        }

        return round(($totalLitres / $totalDistance) * 100, 2);
    }

    /**
     * Fuel efficiency for all vehicles in a company (for reporting).
     * Returns a collection of [ vehicle_id, vehicle_name, plate, efficiency_l100km, total_fills ].
     */
    public function companyFuelEfficiency(int $companyId, string $from, string $to): Collection
    {
        $vehicles = Vehicle::where('company_id', $companyId)->get();

        return $vehicles->map(function (Vehicle $vehicle) use ($from, $to) {
            $fills = FuelLog::where('vehicle_id', $vehicle->id)
                ->whereNotNull('mileage_at_fill')
                ->whereBetween('fill_date', [$from, $to])
                ->orderBy('fill_date')
                ->orderBy('id')
                ->get(['litres', 'mileage_at_fill', 'total_cost']);

            $efficiency    = null;
            $totalLitres   = 0.0;
            $totalDistance = 0.0;
            $totalCost     = (float) $fills->sum('total_cost');

            for ($i = 1; $i < $fills->count(); $i++) {
                $distance = (float) $fills[$i]->mileage_at_fill - (float) $fills[$i - 1]->mileage_at_fill;
                if ($distance > 0) {
                    $totalLitres   += (float) $fills[$i]->litres;
                    $totalDistance += $distance;
                }
            }

            if ($totalDistance > 0) {
                $efficiency = round(($totalLitres / $totalDistance) * 100, 2);
            }

            return (object) [
                'vehicle_id'      => $vehicle->id,
                'vehicle_name'    => $vehicle->name,
                'plate'           => $vehicle->plate,
                'efficiency'      => $efficiency,
                'total_litres'    => round($totalLitres, 2),
                'total_distance'  => round($totalDistance, 1),
                'total_fuel_cost' => $totalCost,
                'total_fills'     => $fills->count(),
            ];
        })->filter(fn ($v) => $v->total_fills > 0);
    }

    /**
     * Vehicle health score: % of scheduled maintenance jobs actually completed (0–100).
     */
    public function healthScore(Vehicle $vehicle): int
    {
        $total = MaintenanceRecord::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['scheduled', 'in_progress', 'completed'])
            ->count();

        if ($total === 0) {
            return 100;
        }

        $completed = MaintenanceRecord::where('vehicle_id', $vehicle->id)
            ->where('status', 'completed')
            ->count();

        return (int) round(($completed / $total) * 100);
    }

    /**
     * Per-vehicle cost summary for a date range (fuel + maintenance).
     */
    public function costSummary(int $companyId, string $from, string $to): Collection
    {
        return Vehicle::where('company_id', $companyId)
            ->get()
            ->map(function (Vehicle $vehicle) use ($from, $to) {
                $fuel = (float) FuelLog::where('vehicle_id', $vehicle->id)
                    ->whereBetween('fill_date', [$from, $to])
                    ->sum('total_cost');

                $maintenance = (float) MaintenanceRecord::where('vehicle_id', $vehicle->id)
                    ->where('status', 'completed')
                    ->whereBetween('service_date', [$from, $to])
                    ->sum('cost');

                return (object) [
                    'vehicle_id'      => $vehicle->id,
                    'vehicle_name'    => $vehicle->name,
                    'plate'           => $vehicle->plate,
                    'fuel_cost'       => $fuel,
                    'maintenance_cost'=> $maintenance,
                    'total_cost'      => $fuel + $maintenance,
                ];
            })
            ->filter(fn ($v) => $v->total_cost > 0)
            ->sortByDesc('total_cost')
            ->values();
    }

    private function recordFuelExpenseInFinance(Vehicle $vehicle, FuelLog $log): void
    {
        try {
            if (class_exists(\Modules\Finance\Services\EnhancedIntegrationService::class)) {
                app(\Modules\Finance\Services\EnhancedIntegrationService::class)
                    ->recordFleetFuelExpense($vehicle, $log);
            }
        } catch (\Throwable $e) {
            Log::warning('Fleet: could not record fuel expense in Finance', [
                'vehicle_id' => $vehicle->id,
                'fuel_log_id' => $log->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function recordMaintenanceExpenseInFinance(Vehicle $vehicle, MaintenanceRecord $record): void
    {
        try {
            if (class_exists(\Modules\Finance\Services\EnhancedIntegrationService::class)) {
                app(\Modules\Finance\Services\EnhancedIntegrationService::class)
                    ->recordFleetMaintenanceExpense($vehicle, $record);
            }
        } catch (\Throwable $e) {
            Log::warning('Fleet: could not record maintenance expense in Finance', [
                'vehicle_id' => $vehicle->id,
                'maintenance_id' => $record->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
