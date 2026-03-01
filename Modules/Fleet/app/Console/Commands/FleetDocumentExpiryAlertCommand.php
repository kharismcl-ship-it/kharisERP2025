<?php

namespace Modules\Fleet\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Fleet\Models\VehicleDocument;

class FleetDocumentExpiryAlertCommand extends Command
{
    protected $signature = 'fleet:document-expiry-alert
                            {--days=30 : Alert when documents expire within this many days}
                            {--company= : Limit to a specific company ID}';

    protected $description = 'Send alerts for fleet vehicle documents expiring within the configured window';

    public function handle(): int
    {
        $days      = (int) $this->option('days');
        $companyId = $this->option('company');

        $documents = VehicleDocument::with(['vehicle', 'vehicle.company'])
            ->whereBetween('expiry_date', [now()->startOfDay(), now()->addDays($days)->endOfDay()])
            ->when($companyId, fn ($q) => $q->whereHas('vehicle', fn ($vq) => $vq->where('company_id', $companyId)))
            ->get();

        if ($documents->isEmpty()) {
            $this->info('No documents expiring within the alert window.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($documents as $document) {
            try {
                $this->sendAlert($document, $days);
                $sent++;
            } catch (\Throwable $e) {
                Log::warning("FleetDocumentExpiryAlert failed for VehicleDocument #{$document->id}: " . $e->getMessage());
                $this->warn("Failed for document #{$document->id}: " . $e->getMessage());
            }
        }

        $this->info("Document expiry alerts sent for {$sent} document(s).");

        return self::SUCCESS;
    }

    private function sendAlert(VehicleDocument $document, int $days): void
    {
        if (! class_exists(\Modules\CommunicationCentre\Models\CommTemplate::class)) {
            return;
        }

        $template = \Modules\CommunicationCentre\Models\CommTemplate::where('code', 'fleet_document_expiry')->first();
        if (! $template) {
            return;
        }

        $vehicle   = $document->vehicle;
        $companyId = $vehicle?->company_id;

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
            'vehicle_name'    => $vehicle?->name ?? '—',
            'plate_number'    => $vehicle?->plate ?? '—',
            'document_type'   => ucfirst($document->type),
            'document_number' => $document->document_number ?? '—',
            'expiry_date'     => $document->expiry_date?->format('d M Y') ?? '—',
            'days_remaining'  => $document->expiry_date
                ? (int) now()->diffInDays($document->expiry_date, false)
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
