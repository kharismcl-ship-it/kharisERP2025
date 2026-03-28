<?php

namespace Modules\ProcurementInventory\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\ProcurementInventory\Models\ProcurementContract;

class CheckProcurementContractsCommand extends Command
{
    protected $signature = 'procurement:check-contracts {--company= : Limit to a specific company ID}';

    protected $description = 'Check procurement contracts expiring within 30 days and send alerts';

    public function handle(): int
    {
        $threshold = now()->addDays(30)->toDateString();

        $query = ProcurementContract::with(['vendor', 'company'])
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', $threshold)
            ->where('end_date', '>=', now()->toDateString());

        if ($companyId = $this->option('company')) {
            $query->where('company_id', $companyId);
        }

        $contracts = $query->get();

        if ($contracts->isEmpty()) {
            $this->info('No contracts expiring within 30 days.');
            return self::SUCCESS;
        }

        foreach ($contracts as $contract) {
            try {
                $this->sendAlert($contract);
            } catch (\Throwable $e) {
                Log::warning("CheckContracts alert failed for #{$contract->id}: " . $e->getMessage());
            }
        }

        $this->info("Alerts sent for {$contracts->count()} expiring contract(s).");

        return self::SUCCESS;
    }

    private function sendAlert(ProcurementContract $contract): void
    {
        if (! class_exists(\Modules\CommunicationCentre\Services\CommunicationService::class)) {
            return;
        }

        $recipients = \App\Models\User::whereHas('companies', fn ($q) => $q->where('companies.id', $contract->company_id))
            ->where('is_active', true)
            ->take(5)
            ->pluck('email')
            ->filter()
            ->toArray();

        if (empty($recipients)) {
            return;
        }

        $daysLeft = now()->diffInDays($contract->end_date, false);
        $subject  = "Contract Expiring: {$contract->contract_number} — {$contract->title}";
        $body     = "Contract {$contract->contract_number} with {$contract->vendor?->name} expires on {$contract->end_date?->format('Y-m-d')} ({$daysLeft} day(s) remaining). Please review and renew if needed.";

        $service = app(\Modules\CommunicationCentre\Services\CommunicationService::class);
        foreach ($recipients as $email) {
            $service->sendRawEmail($email, $subject, $body);
        }
    }
}
