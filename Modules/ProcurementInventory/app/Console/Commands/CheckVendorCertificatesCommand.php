<?php

namespace Modules\ProcurementInventory\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\ProcurementInventory\Models\VendorCertificate;

class CheckVendorCertificatesCommand extends Command
{
    protected $signature = 'procurement:check-certificates {--company= : Limit to a specific company ID}';

    protected $description = 'Check vendor certificates expiring within 30 days and update statuses';

    public function handle(): int
    {
        $query = VendorCertificate::with(['vendor', 'company'])
            ->whereNotNull('expiry_date');

        if ($companyId = $this->option('company')) {
            $query->where('company_id', $companyId);
        }

        $certificates = $query->get();

        $expiring = 0;
        $expired  = 0;

        foreach ($certificates as $cert) {
            $oldStatus = $cert->status;
            // Status is auto-set in the model's saving hook — trigger via save()
            $cert->save();

            if ($cert->status === 'expiring_soon' && $oldStatus !== 'expiring_soon') {
                $this->sendAlert($cert, 'expiring_soon');
                $expiring++;
            } elseif ($cert->status === 'expired' && $oldStatus !== 'expired') {
                $this->sendAlert($cert, 'expired');
                $expired++;
            }
        }

        $this->info("Certificates: {$expiring} newly expiring soon, {$expired} newly expired.");

        return self::SUCCESS;
    }

    private function sendAlert(VendorCertificate $cert, string $type): void
    {
        if (! class_exists(\Modules\CommunicationCentre\Services\CommunicationService::class)) {
            return;
        }

        try {
            $recipients = \App\Models\User::whereHas('companies', fn ($q) => $q->where('companies.id', $cert->company_id))
                ->where('is_active', true)
                ->take(5)
                ->pluck('email')
                ->filter()
                ->toArray();

            if (empty($recipients)) {
                return;
            }

            $subject = $type === 'expired'
                ? "Certificate Expired: {$cert->vendor?->name} — {$cert->certificate_type}"
                : "Certificate Expiring Soon: {$cert->vendor?->name} — {$cert->certificate_type}";

            $body = $type === 'expired'
                ? "The {$cert->certificate_type} certificate for {$cert->vendor?->name} expired on {$cert->expiry_date?->format('Y-m-d')}. Please arrange renewal."
                : "The {$cert->certificate_type} certificate for {$cert->vendor?->name} expires on {$cert->expiry_date?->format('Y-m-d')} (within 30 days). Please arrange renewal.";

            $service = app(\Modules\CommunicationCentre\Services\CommunicationService::class);
            foreach ($recipients as $email) {
                $service->sendRawEmail($email, $subject, $body);
            }
        } catch (\Throwable $e) {
            Log::warning("CheckCertificates alert failed for cert #{$cert->id}: " . $e->getMessage());
        }
    }
}