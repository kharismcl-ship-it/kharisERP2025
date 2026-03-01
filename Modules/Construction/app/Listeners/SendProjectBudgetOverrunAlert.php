<?php

namespace Modules\Construction\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Construction\Events\ProjectBudgetOverrun;

class SendProjectBudgetOverrunAlert
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(ProjectBudgetOverrun $event): void
    {
        $project = $event->project;

        $data = [
            'project_name'   => $project->name,
            'client_name'    => $project->client_name ?? 'Client',
            'budget'         => number_format((float) $project->budget, 2),
            'total_spent'    => number_format((float) $project->total_spent, 2),
            'overrun_amount' => number_format($event->overrunAmount, 2),
            'overrun_pct'    => $project->budget > 0
                ? number_format(($event->overrunAmount / (float) $project->budget) * 100, 1)
                : '0',
            'currency'       => 'GHS',
        ];

        try {
            $this->comms->sendToModel(
                $project,
                'email',
                'construction_budget_overrun',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendProjectBudgetOverrunAlert failed', [
                'project_id'    => $project->id,
                'overrun_amount' => $event->overrunAmount,
                'error'         => $e->getMessage(),
            ]);
        }
    }
}
