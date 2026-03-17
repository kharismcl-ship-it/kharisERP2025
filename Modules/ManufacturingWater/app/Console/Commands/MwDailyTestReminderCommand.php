<?php

namespace Modules\ManufacturingWater\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\ManufacturingWater\Models\MwPlant;
use Modules\ManufacturingWater\Models\MwWaterTestRecord;

class MwDailyTestReminderCommand extends Command
{
    protected $signature = 'mw:daily-test-reminder
                            {--company= : Limit to a specific company ID}';

    protected $description = 'Send daily reminders to submit water quality test records for active plants';

    public function handle(): int
    {
        $companyId = $this->option('company');
        $today     = now()->startOfDay();

        $plants = MwPlant::with(['company'])
            ->where('is_active', true)
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->get();

        $sent = 0;
        foreach ($plants as $plant) {
            // Check if a test has already been recorded today for this plant
            $testedToday = MwWaterTestRecord::where('plant_id', $plant->id)
                ->where('test_date', '>=', $today)
                ->exists();

            if ($testedToday) {
                $this->line("Plant '{$plant->name}' already has a test recorded today — skipping.");
                continue;
            }

            try {
                $this->sendReminder($plant);
                $sent++;
            } catch (\Throwable $e) {
                Log::warning("MwDailyTestReminder failed for plant '{$plant->name}': " . $e->getMessage());
                $this->warn("Failed for plant '{$plant->name}': " . $e->getMessage());
            }
        }

        $this->info("Daily test reminders sent for {$sent} plant(s).");

        return self::SUCCESS;
    }

    private function sendReminder(MwPlant $plant): void
    {
        if (! class_exists(\Modules\CommunicationCentre\Services\CommunicationService::class)) {
            return;
        }

        $companyId = $plant->company_id;
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

        $date    = now()->format('d M Y');
        $subject = "Daily Water Test Reminder: {$plant->name} — {$date}";
        $body    = "No water quality test has been recorded yet today ({$date}) for plant '{$plant->name}'. "
            . "Please ensure the daily water test is completed and submitted in the system. "
            . "Consistent testing is required for regulatory compliance.";

        $service = app(\Modules\CommunicationCentre\Services\CommunicationService::class);

        foreach ($recipients as $email) {
            $service->sendRawEmail($email, $email, $subject, $body);
        }
    }
}
