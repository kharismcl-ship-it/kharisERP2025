<?php

namespace Modules\ManufacturingWater\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\ManufacturingWater\Events\MwWaterTestFailed;

class SendWaterTestFailureAlert
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(MwWaterTestFailed $event): void
    {
        $record = $event->waterTestRecord;

        $data = [
            'test_date'          => $record->test_date?->format('d M Y') ?? now()->format('d M Y'),
            'test_type'          => ucfirst($record->test_type),
            'tested_by'          => $record->tested_by ?? 'Lab Team',
            'ph'                 => $record->ph,
            'turbidity_ntu'      => $record->turbidity_ntu,
            'tds_ppm'            => $record->tds_ppm,
            'coliform_count'     => $record->coliform_count,
            'chlorine_residual'  => $record->chlorine_residual,
            'notes'              => $record->notes ?? 'No notes',
        ];

        try {
            $this->comms->sendToModel(
                $record,
                'email',
                'mw_water_test_failed',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendWaterTestFailureAlert failed', [
                'water_test_record_id' => $record->id,
                'error'                => $e->getMessage(),
            ]);
        }
    }
}