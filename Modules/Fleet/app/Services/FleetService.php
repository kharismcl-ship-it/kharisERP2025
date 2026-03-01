<?php

namespace Modules\Fleet\Services;

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
