<?php

namespace Modules\Farms\Console\Commands;

use Illuminate\Console\Command;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Farms\Models\CropCycle;

class FarmsHarvestDueAlertCommand extends Command
{
    protected $signature   = 'farms:harvest-due-alerts {--days=3 : Days ahead to look for upcoming harvests}';
    protected $description = 'Send harvest due alerts for crop cycles approaching or past their expected harvest date.';

    public function handle(CommunicationService $comms): int
    {
        $daysAhead = (int) $this->option('days');

        $cycles = CropCycle::query()
            ->where('status', 'growing')
            ->whereNotNull('expected_harvest_date')
            ->whereDate('expected_harvest_date', '<=', now()->addDays($daysAhead))
            ->with(['farm', 'company'])
            ->get();

        if ($cycles->isEmpty()) {
            $this->info('No harvest due alerts to send.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($cycles as $cycle) {
            $farm = $cycle->farm;
            if (! $farm) {
                continue;
            }

            $daysUntil = now()->diffInDays($cycle->expected_harvest_date, false);
            $label     = $daysUntil < 0
                ? abs($daysUntil) . ' days overdue'
                : ($daysUntil === 0 ? 'due today' : "due in {$daysUntil} days");

            $params = [
                'farm_name'       => $farm->name,
                'crop_name'       => $cycle->crop_name,
                'expected_date'   => $cycle->expected_harvest_date->format('d M Y'),
                'status'          => $label,
                'variety'         => $cycle->variety ?? 'N/A',
                'planted_area'    => $cycle->planted_area
                    ? $cycle->planted_area . ' ' . $cycle->planted_area_unit
                    : 'N/A',
            ];

            try {
                if ($farm->contact_email) {
                    $comms->sendToContact(
                        channel: 'email',
                        toEmail: $farm->contact_email,
                        toPhone: null,
                        subject: null,
                        templateCode: 'farms_harvest_due_email',
                        data: $params
                    );
                }

                if ($farm->owner_phone) {
                    $comms->sendToContact(
                        channel: 'sms',
                        toEmail: null,
                        toPhone: $farm->owner_phone,
                        subject: null,
                        templateCode: 'farms_harvest_due_sms',
                        data: $params
                    );
                }

                $sent++;
            } catch (\Throwable $e) {
                $this->error("Failed to send alert for {$cycle->crop_name} ({$farm->name}): " . $e->getMessage());
            }
        }

        $this->info("Sent {$sent} harvest due alert(s) for {$cycles->count()} cycle(s).");
        return self::SUCCESS;
    }
}