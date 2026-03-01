<?php

namespace Modules\ManufacturingWater\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\ManufacturingWater\Events\MwDistributionCompleted;

class SendDistributionCompletedAlert
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(MwDistributionCompleted $event): void
    {
        $record = $event->distributionRecord;

        $data = [
            'distribution_reference' => $record->distribution_reference,
            'destination'            => $record->destination,
            'distribution_date'      => $record->distribution_date?->format('d M Y') ?? now()->format('d M Y'),
            'volume_liters'          => number_format((float) $record->volume_liters, 2),
            'unit_price'             => number_format((float) $record->unit_price, 4),
            'total_amount'           => number_format((float) $record->total_amount, 2),
            'currency'               => 'GHS',
            'notes'                  => $record->notes ?? '',
        ];

        try {
            $this->comms->sendToModel(
                $record,
                'email',
                'mw_distribution_completed',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendDistributionCompletedAlert failed', [
                'distribution_record_id' => $record->id,
                'error'                  => $e->getMessage(),
            ]);
        }
    }
}