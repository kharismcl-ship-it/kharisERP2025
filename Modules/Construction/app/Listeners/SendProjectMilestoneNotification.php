<?php

namespace Modules\Construction\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Construction\Events\ProjectMilestoneCompleted;

class SendProjectMilestoneNotification
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(ProjectMilestoneCompleted $event): void
    {
        $project = $event->project;
        $phase   = $event->phase;

        $data = [
            'project_name'   => $project->name,
            'phase_name'     => $phase->name,
            'client_name'    => $project->client_name ?? 'Client',
            'progress'       => $project->overall_progress . '%',
            'contract_value' => number_format((float) $project->contract_value, 2),
            'currency'       => 'GHS',
        ];

        try {
            $this->comms->sendToModel(
                $project,
                'email',
                'construction_project_milestone',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendProjectMilestoneNotification failed', [
                'project_id' => $project->id,
                'phase_id'   => $phase->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
