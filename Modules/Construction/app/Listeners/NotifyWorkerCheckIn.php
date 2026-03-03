<?php

namespace Modules\Construction\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Construction\Events\WorkerCheckedIn;

class NotifyWorkerCheckIn
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(WorkerCheckedIn $event): void
    {
        $worker     = $event->worker;
        $attendance = $event->attendance;
        $project    = $worker->project;

        if (!$project) {
            return;
        }

        $data = [
            'worker_name'    => $worker->name,
            'check_in_time'  => $attendance->check_in_time,
            'project_name'   => $project->name,
            'date'           => $attendance->date?->format('d M Y'),
        ];

        try {
            $this->comms->sendToModel(
                $project,
                'sms',
                'construction_worker_checked_in',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('NotifyWorkerCheckIn failed', [
                'worker_id' => $worker->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }
}
