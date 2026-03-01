<?php

namespace Modules\Farms\Console\Commands;

use Illuminate\Console\Command;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Farms\Models\LivestockHealthRecord;

class FarmsLivestockHealthReminderCommand extends Command
{
    protected $signature   = 'farms:livestock-health-reminders {--days=7 : Days ahead to look for due treatments}';
    protected $description = 'Send reminders for livestock health treatments due within the configured window.';

    public function handle(CommunicationService $comms): int
    {
        $daysAhead = (int) $this->option('days');

        $records = LivestockHealthRecord::query()
            ->whereNotNull('next_due_date')
            ->whereDate('next_due_date', '<=', now()->addDays($daysAhead))
            ->whereDate('next_due_date', '>=', now())
            ->with(['livestockBatch.farm', 'company'])
            ->get();

        if ($records->isEmpty()) {
            $this->info('No livestock health reminders to send.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($records as $record) {
            $batch = $record->livestockBatch;
            $farm  = $batch?->farm;

            if (! $farm) {
                continue;
            }

            $params = [
                'farm_name'       => $farm->name,
                'batch_reference' => $batch->batch_reference ?? "Batch #{$batch->id}",
                'animal_type'     => ucfirst($batch->animal_type),
                'current_count'   => $batch->current_count,
                'event_type'      => ucwords(str_replace('_', ' ', $record->event_type)),
                'medicine_used'   => $record->medicine_used ?? 'N/A',
                'next_due_date'   => $record->next_due_date->format('d M Y'),
            ];

            try {
                if ($farm->contact_email) {
                    $comms->sendToContact(
                        channel: 'email',
                        toEmail: $farm->contact_email,
                        toPhone: null,
                        subject: null,
                        templateCode: 'farms_livestock_health_reminder_email',
                        data: $params
                    );
                }

                if ($farm->owner_phone) {
                    $comms->sendToContact(
                        channel: 'sms',
                        toEmail: null,
                        toPhone: $farm->owner_phone,
                        subject: null,
                        templateCode: 'farms_livestock_health_reminder_sms',
                        data: $params
                    );
                }

                $sent++;
            } catch (\Throwable $e) {
                $this->error("Failed health reminder for {$batch->batch_reference} ({$farm->name}): " . $e->getMessage());
            }
        }

        $this->info("Sent {$sent} livestock health reminder(s) for {$records->count()} record(s).");
        return self::SUCCESS;
    }
}