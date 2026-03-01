<?php

namespace Modules\Fleet\Services\Automation;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Core\Models\AutomationSetting;
use Modules\Fleet\Models\MaintenanceRecord;

/**
 * Sends maintenance reminders for vehicles whose next scheduled service
 * is within the configured lead time (default 7 days).
 */
class MaintenanceReminderHandler
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function execute(AutomationSetting $setting): array
    {
        $companyId = $setting->company_id;
        $leadDays  = (int) ($setting->config['lead_days'] ?? 7);
        $processed = 0;
        $errors    = [];

        $upcomingWindow = now()->addDays($leadDays)->toDateString();

        // Find maintenance records with a next_service_date within the lead window
        // that are not yet completed and haven't been reminded recently
        $records = MaintenanceRecord::with('vehicle')
            ->where('company_id', $companyId)
            ->where('status', '!=', 'completed')
            ->whereNotNull('next_service_date')
            ->whereDate('next_service_date', '<=', $upcomingWindow)
            ->whereDate('next_service_date', '>=', now()->toDateString())
            ->get();

        foreach ($records as $record) {
            $vehicle = $record->vehicle;
            if (! $vehicle) {
                continue;
            }

            $daysUntil = (int) now()->startOfDay()->diffInDays($record->next_service_date);

            $data = [
                'vehicle_name'       => $vehicle->display_name ?? "{$vehicle->make} {$vehicle->model} – {$vehicle->plate}",
                'plate'              => $vehicle->plate ?? 'N/A',
                'maintenance_type'   => ucfirst(str_replace('_', ' ', $record->type ?? 'service')),
                'next_service_date'  => $record->next_service_date->format('d M Y'),
                'days_until_service' => $daysUntil,
                'current_mileage'    => number_format((float) $vehicle->current_mileage, 0),
                'reminder_date'      => now()->format('d M Y'),
            ];

            try {
                $this->comms->sendToModel(
                    $record,
                    'email',
                    'fleet_maintenance_reminder',
                    $data
                );
                $processed++;
            } catch (\Throwable $e) {
                $errors[] = "Vehicle {$vehicle->plate} (record {$record->id}): {$e->getMessage()}";
                Log::warning('MaintenanceReminderHandler notification failed', [
                    'maintenance_record_id' => $record->id,
                    'vehicle_id'            => $vehicle->id,
                    'error'                 => $e->getMessage(),
                ]);
            }
        }

        return [
            'success'           => empty($errors),
            'records_processed' => $processed,
            'details'           => [
                'vehicles_checked' => $records->count(),
                'reminders_sent'   => $processed,
                'lead_days'        => $leadDays,
                'errors'           => $errors,
            ],
        ];
    }
}