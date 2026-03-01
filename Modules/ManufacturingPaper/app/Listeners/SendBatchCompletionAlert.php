<?php

namespace Modules\ManufacturingPaper\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\ManufacturingPaper\Events\MpBatchCompleted;

class SendBatchCompletionAlert
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(MpBatchCompleted $event): void
    {
        $batch = $event->batch;

        $data = [
            'batch_number'      => $batch->batch_number,
            'paper_grade'       => $batch->paperGrade?->name ?? 'N/A',
            'quantity_produced' => number_format((float) $batch->quantity_produced, 3),
            'unit'              => $batch->unit,
            'waste_quantity'    => number_format((float) $batch->waste_quantity, 3),
            'efficiency_pct'    => $batch->efficiency_percent ?? '0',
            'production_cost'   => number_format((float) $batch->production_cost, 2),
            'currency'          => 'GHS',
            'end_time'          => $batch->end_time?->format('d M Y H:i') ?? now()->format('d M Y H:i'),
        ];

        try {
            $this->comms->sendToModel(
                $batch,
                'email',
                'mp_batch_completed',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendBatchCompletionAlert failed', [
                'batch_id' => $batch->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}