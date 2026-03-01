<?php

namespace Modules\ManufacturingPaper\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\ManufacturingPaper\Events\MpQualityFailed;

class SendQualityFailureAlert
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(MpQualityFailed $event): void
    {
        $record = $event->qualityRecord;
        $batch  = $record->batch;

        if (! $batch) {
            return;
        }

        $data = [
            'batch_number'    => $batch->batch_number,
            'paper_grade'     => $batch->paperGrade?->name ?? 'N/A',
            'test_date'       => $record->test_date?->format('d M Y') ?? now()->format('d M Y'),
            'tested_by'       => $record->tested_by ?? 'QC Team',
            'tensile_cd'      => $record->tensile_cd,
            'tensile_md'      => $record->tensile_md,
            'burst_strength'  => $record->burst_strength,
            'moisture_percent'=> $record->moisture_percent,
            'brightness'      => $record->brightness,
            'notes'           => $record->notes ?? 'No notes',
        ];

        try {
            $this->comms->sendToModel(
                $batch,
                'email',
                'mp_quality_failed',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendQualityFailureAlert failed', [
                'quality_record_id' => $record->id,
                'batch_id'          => $batch->id,
                'error'             => $e->getMessage(),
            ]);
        }
    }
}