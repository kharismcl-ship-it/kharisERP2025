<?php

namespace Modules\ManufacturingPaper\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\ManufacturingPaper\Models\MpProductionBatch;

class MpBatchOverdueAlertCommand extends Command
{
    protected $signature = 'mp:batch-overdue-alert
                            {--company= : Limit to a specific company ID}';

    protected $description = 'Alert managers about paper production batches that are in-progress beyond their expected completion time';

    public function handle(): int
    {
        $companyId = $this->option('company');

        $batches = MpProductionBatch::with(['plant', 'plant.company', 'productionLine'])
            ->where('status', 'in_progress')
            ->whereNotNull('end_time')
            ->where('end_time', '<', now())
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->get();

        if ($batches->isEmpty()) {
            $this->info('No overdue production batches found.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($batches as $batch) {
            try {
                $this->sendAlert($batch);
                $sent++;
            } catch (\Throwable $e) {
                Log::warning("MpBatchOverdueAlert failed for batch #{$batch->id}: " . $e->getMessage());
                $this->warn("Failed for batch #{$batch->id}: " . $e->getMessage());
            }
        }

        $this->info("Overdue batch alerts sent for {$sent} batch(es).");

        return self::SUCCESS;
    }

    private function sendAlert(MpProductionBatch $batch): void
    {
        if (! class_exists(\Modules\CommunicationCentre\Services\CommunicationService::class)) {
            return;
        }

        $companyId = $batch->company_id ?? $batch->plant?->company_id;
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

        $hoursOverdue = (int) now()->diffInHours($batch->end_time, false) * -1;
        $subject      = "Production Batch {$batch->batch_number} Overdue";
        $body         = "Batch {$batch->batch_number} at plant '{$batch->plant?->name}' "
            . "was scheduled to complete by {$batch->end_time?->format('d M Y H:i')} "
            . "and is now {$hoursOverdue} hour(s) overdue. "
            . "Please review and update the batch status.";

        $service = app(\Modules\CommunicationCentre\Services\CommunicationService::class);

        foreach ($recipients as $email) {
            $service->sendRawEmail($email, $email, $subject, $body);
        }
    }
}
