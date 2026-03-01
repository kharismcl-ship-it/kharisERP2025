<?php

namespace Modules\Farms\Console\Commands;

use Illuminate\Console\Command;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Farms\Models\FarmTask;

class FarmsTaskOverdueAlertCommand extends Command
{
    protected $signature   = 'farms:task-overdue-alerts {--days=0 : Extra grace days beyond due date before alerting}';
    protected $description = 'Send overdue alerts for farm tasks that are past their due date and not yet completed.';

    public function handle(CommunicationService $comms): int
    {
        $graceDays = (int) $this->option('days');

        $tasks = FarmTask::query()
            ->whereNull('completed_at')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->subDays($graceDays))
            ->with(['farm', 'assignedWorker', 'company'])
            ->get();

        if ($tasks->isEmpty()) {
            $this->info('No overdue task alerts to send.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ($tasks as $task) {
            $farm = $task->farm;
            if (! $farm) {
                continue;
            }

            $params = [
                'farm_name'   => $farm->name,
                'task_title'  => $task->title,
                'task_type'   => ucwords(str_replace('_', ' ', $task->task_type)),
                'priority'    => ucfirst($task->priority),
                'due_date'    => $task->due_date->format('d M Y'),
                'assigned_to' => $task->assignedWorker?->display_name ?? 'Unassigned',
            ];

            try {
                if ($farm->contact_email) {
                    $comms->sendToContact(
                        channel: 'email',
                        toEmail: $farm->contact_email,
                        toPhone: null,
                        subject: null,
                        templateCode: 'farms_task_overdue_email',
                        data: $params
                    );
                }

                if ($farm->owner_phone) {
                    $comms->sendToContact(
                        channel: 'sms',
                        toEmail: null,
                        toPhone: $farm->owner_phone,
                        subject: null,
                        templateCode: 'farms_task_overdue_sms',
                        data: $params
                    );
                }

                $sent++;
            } catch (\Throwable $e) {
                $this->error("Failed to send alert for task #{$task->id} ({$farm->name}): " . $e->getMessage());
            }
        }

        $this->info("Sent {$sent} task overdue alert(s) for {$tasks->count()} overdue task(s).");
        return self::SUCCESS;
    }
}