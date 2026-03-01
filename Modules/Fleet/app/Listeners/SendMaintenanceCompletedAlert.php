<?php

namespace Modules\Fleet\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Fleet\Events\MaintenanceCompleted;

class SendMaintenanceCompletedAlert
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(MaintenanceCompleted $event): void
    {
        $record  = $event->maintenanceRecord;
        $vehicle = $record->vehicle;

        if (! $vehicle) {
            return;
        }

        $data = [
            'vehicle_name'     => $vehicle->display_name ?? "{$vehicle->make} {$vehicle->model} – {$vehicle->plate}",
            'maintenance_type' => ucfirst(str_replace('_', ' ', $record->type)),
            'service_date'     => $record->service_date?->format('d M Y') ?? now()->format('d M Y'),
            'service_provider' => $record->service_provider ?? 'N/A',
            'cost'             => number_format((float) $record->cost, 2),
            'currency'         => 'GHS',
            'next_service_date'=> $record->next_service_date?->format('d M Y') ?? 'Not set',
        ];

        try {
            $this->comms->sendToModel(
                $record,
                'email',
                'fleet_maintenance_completed',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendMaintenanceCompletedAlert failed', [
                'maintenance_record_id' => $record->id,
                'vehicle_id'            => $vehicle->id,
                'error'                 => $e->getMessage(),
            ]);
        }
    }
}