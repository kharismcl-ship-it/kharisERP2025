<?php

namespace Modules\ManufacturingPaper\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\ManufacturingPaper\Models\MpQualityRecord;

class MpQualityFailureAlertCommand extends Command
{
    protected $signature = 'mp:quality-failure-alert
                            {--hours=24 : Look back this many hours for failed records}
                            {--company= : Limit to a specific company ID}';

    protected $description = 'Alert managers about paper quality test failures recorded in the last N hours';

    public function handle(): int
    {
        $hours     = (int) $this->option('hours');
        $companyId = $this->option('company');

        $failures = MpQualityRecord::with(['batch', 'batch.plant'])
            ->where('passed', false)
            ->where('created_at', '>=', now()->subHours($hours))
            ->when($companyId, fn ($q) => $q->whereHas('batch', fn ($bq) => $bq->where('company_id', $companyId)))
            ->get();

        if ($failures->isEmpty()) {
            $this->info("No quality failures in the last {$hours} hour(s).");
            return self::SUCCESS;
        }

        // Group by batch to send one alert per batch with multiple failures
        $byBatch = $failures->groupBy('production_batch_id');

        $sent = 0;
        foreach ($byBatch as $batchId => $records) {
            try {
                $this->sendAlert($records->first()->batch, $records->count());
                $sent++;
            } catch (\Throwable $e) {
                Log::warning("MpQualityFailureAlert failed for batch #{$batchId}: " . $e->getMessage());
                $this->warn("Failed for batch #{$batchId}: " . $e->getMessage());
            }
        }

        $this->info("Quality failure alerts sent for {$sent} batch(es).");

        return self::SUCCESS;
    }

    private function sendAlert($batch, int $failureCount): void
    {
        if (! $batch || ! class_exists(\Modules\CommunicationCentre\Services\CommunicationService::class)) {
            return;
        }

        $companyId = $batch->company_id;
        if (! $companyId) {
            return;
        }

        $recipients = \App\Models\User::whereHas('companies', fn ($q) => $q->where('companies.id', $companyId))
            ->where('is_active', true)
            ->take(5)
            ->pluck('email')
            ->filter()
            ->toArray();

        if (empty($recipients)) {
            return;
        }

        $subject = "Quality Failure: Batch {$batch->batch_number}";
        $body    = "{$failureCount} quality test(s) FAILED for production batch {$batch->batch_number} "
            . "at plant '{$batch->plant?->name}'. "
            . "Please review the quality records and take corrective action before distribution.";

        $service = app(\Modules\CommunicationCentre\Services\CommunicationService::class);

        foreach ($recipients as $email) {
            $service->sendRawEmail($email, $email, $subject, $body);
        }
    }
}
