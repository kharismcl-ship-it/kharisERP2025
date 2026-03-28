<?php

namespace Modules\Farms\Console\Commands;

use Illuminate\Console\Command;
use Modules\Farms\Models\FarmCertification;

class CheckCertificationExpiryCommand extends Command
{
    protected $signature = 'farms:check-certifications';

    protected $description = 'Check for farm certifications expiring soon and send renewal reminders';

    public function handle(): void
    {
        $expiring = FarmCertification::where('status', 'active')
            ->whereNotNull('expiry_date')
            ->get()
            ->filter(fn ($c) => $c->daysUntilExpiry() !== null && $c->daysUntilExpiry() <= $c->renewal_reminder_days);

        if ($expiring->isEmpty()) {
            $this->info('No certifications approaching expiry.');
            return;
        }

        foreach ($expiring as $cert) {
            $days = $cert->daysUntilExpiry();
            $farmName = $cert->farm?->name ?? "Farm #{$cert->farm_id}";
            $this->info("Expiring: {$farmName} — {$cert->certification_type} expires in {$days} days");
            \Illuminate\Support\Facades\Log::warning('Farm certification expiring', [
                'farm'             => $farmName,
                'type'             => $cert->certification_type,
                'certificate_number' => $cert->certificate_number,
                'expiry_date'      => $cert->expiry_date?->toDateString(),
                'days_remaining'   => $days,
                'company_id'       => $cert->company_id,
            ]);

            // Notify via CommunicationCentre if available
            try {
                if (class_exists(\Modules\CommunicationCentre\Services\CommunicationService::class)) {
                    $service = app(\Modules\CommunicationCentre\Services\CommunicationService::class);
                    $service->sendToCompanyAdmins(
                        companyId: $cert->company_id,
                        subject: "Certification Expiry Alert: {$cert->certification_type}",
                        message: "The {$cert->certification_type} certification for {$farmName} expires in {$days} day(s) on {$cert->expiry_date?->toDateString()}. Please initiate renewal.",
                    );
                }
            } catch (\Throwable) {
                // CommunicationCentre may not be available — silently continue
            }
        }

        $this->info("Checked {$expiring->count()} expiring certification(s).");
    }
}