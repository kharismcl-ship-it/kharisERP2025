<?php

namespace Modules\ManufacturingWater\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\ManufacturingWater\Models\MwTankLevel;

class MwTankLevelAlertCommand extends Command
{
    protected $signature = 'mw:tank-level-alert
                            {--threshold=20 : Alert when fill percentage falls below this value}
                            {--company= : Limit to a specific company ID}';

    protected $description = 'Alert operators when water tank fill percentage is critically low';

    public function handle(): int
    {
        $threshold = (int) $this->option('threshold');
        $companyId = $this->option('company');

        // Get the most recent reading per tank (plant_id + tank_name combination)
        $recentReadings = MwTankLevel::with(['plant', 'plant.company'])
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->orderByDesc('recorded_at')
            ->get()
            ->unique(fn ($r) => $r->plant_id . '::' . $r->tank_name)
            ->filter(fn ($r) => $r->fill_percentage < $threshold);

        if ($recentReadings->isEmpty()) {
            $this->info("No tanks below {$threshold}% fill threshold.");
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($recentReadings as $reading) {
            try {
                $this->sendAlert($reading, $threshold);
                $sent++;
            } catch (\Throwable $e) {
                Log::warning("MwTankLevelAlert failed for tank '{$reading->tank_name}': " . $e->getMessage());
                $this->warn("Failed for tank '{$reading->tank_name}': " . $e->getMessage());
            }
        }

        $this->info("Low tank level alerts sent for {$sent} tank(s).");

        return self::SUCCESS;
    }

    private function sendAlert(MwTankLevel $reading, int $threshold): void
    {
        if (! class_exists(\Modules\CommunicationCentre\Services\CommunicationService::class)) {
            return;
        }

        $companyId = $reading->company_id ?? $reading->plant?->company_id;
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

        $pct     = $reading->fill_percentage;
        $subject = "Low Tank Level Warning: {$reading->tank_name}";
        $body    = "Tank '{$reading->tank_name}' at plant '{$reading->plant?->name}' "
            . "is at {$pct}% capacity ({$reading->current_level_liters}L of {$reading->capacity_liters}L). "
            . "This is below the {$threshold}% alert threshold. Please arrange refilling.";

        $service = app(\Modules\CommunicationCentre\Services\CommunicationService::class);

        foreach ($recipients as $email) {
            $service->sendRawEmail($email, $email, $subject, $body);
        }
    }
}
