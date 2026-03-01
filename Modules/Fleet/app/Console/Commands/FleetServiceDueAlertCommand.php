<?php

namespace Modules\Fleet\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Fleet\Models\MaintenanceRecord;

class FleetServiceDueAlertCommand extends Command
{
    protected $signature = 'fleet:service-due-alert
                            {--days=14 : Alert for services due within this many days}
                            {--company= : Limit to a specific company ID}';

    protected $description = 'Send alerts for scheduled fleet services due within the configured window';

    public function handle(): int
    {
        $days      = (int) $this->option('days');
        $companyId = $this->option('company');

        // Scheduled maintenance records with a next_service_date within the window
        $records = MaintenanceRecord::with(['vehicle'])
            ->where('status', 'scheduled')
            ->whereBetween('service_date', [now()->startOfDay(), now()->addDays($days)->endOfDay()])
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->get();

        if ($records->isEmpty()) {
            $this->info('No services due within the alert window.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($records as $record) {
            try {
                $this->sendAlert($record, $days);
                $sent++;
            } catch (\Throwable $e) {
                Log::warning("FleetServiceDueAlert failed for MaintenanceRecord #{$record->id}: " . $e->getMessage());
                $this->warn("Failed for record #{$record->id}: " . $e->getMessage());
            }
        }

        $this->info("Service due alerts sent for {$sent} record(s).");

        return self::SUCCESS;
    }

    private function sendAlert(MaintenanceRecord $record, int $days): void
    {
        if (! class_exists(\Modules\CommunicationCentre\Models\CommTemplate::class)) {
            return;
        }

        $template = \Modules\CommunicationCentre\Models\CommTemplate::where('code', 'fleet_service_due')->first();
        if (! $template) {
            return;
        }

        $companyId = $record->company_id;

        $recipients = \App\Models\User::whereHas('companies', fn ($q) => $q->where('companies.id', $companyId))
            ->where('is_active', true)
            ->take(5)
            ->pluck('email')
            ->filter()
            ->toArray();

        if (empty($recipients)) {
            return;
        }

        $variables = [
            'vehicle_name'     => $record->vehicle?->name ?? '—',
            'plate_number'     => $record->vehicle?->plate ?? '—',
            'service_type'     => ucwords(str_replace('_', ' ', $record->type)),
            'service_date'     => $record->service_date?->format('d M Y') ?? '—',
            'service_provider' => $record->service_provider ?? '—',
            'description'      => $record->description ?? '—',
            'days_until'       => $record->service_date
                ? (int) now()->diffInDays($record->service_date, false)
                : 0,
        ];

        $service = app(\Modules\CommunicationCentre\Services\CommunicationService::class);

        foreach ($recipients as $email) {
            $variables['recipient_name'] = $email;
            $service->sendFromTemplate(
                $template,
                ['email' => $email],
                $variables
            );
        }
    }
}
