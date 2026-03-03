<?php

namespace Modules\Farms\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Farms\Events\FarmWorkerAssigned;

class NotifyFarmWorkerAssigned
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(FarmWorkerAssigned $event): void
    {
        $task   = $event->task;
        $worker = $task->assignedWorker ?? null;

        $workerEmail = $worker?->employee?->email ?? null;

        if (! $workerEmail) {
            return;
        }

        $data = [
            'farm_name'   => $task->farm?->name ?? 'Farm',
            'task_title'  => $task->title,
            'task_type'   => ucwords(str_replace('_', ' ', $task->task_type ?? 'task')),
            'priority'    => ucfirst($task->priority ?? 'normal'),
            'due_date'    => $task->due_date?->format('d M Y') ?? 'No due date',
            'description' => $task->description ?? '',
            'worker_name' => $worker->display_name,
        ];

        try {
            $this->comms->sendToContact(
                'email',
                $workerEmail,
                null,
                null,
                'farms_worker_task_assigned',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('NotifyFarmWorkerAssigned failed', [
                'farm_task_id' => $task->id,
                'error'        => $e->getMessage(),
            ]);
        }
    }
}
