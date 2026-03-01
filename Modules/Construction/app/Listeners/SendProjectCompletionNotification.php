<?php

namespace Modules\Construction\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Construction\Events\ProjectCompleted;

class SendProjectCompletionNotification
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(ProjectCompleted $event): void
    {
        $project = $event->project;

        $data = [
            'project_name'    => $project->name,
            'client_name'     => $project->client_name ?? 'Client',
            'actual_end_date' => $project->actual_end_date?->format('d M Y') ?? now()->format('d M Y'),
            'contract_value'  => number_format((float) $project->contract_value, 2),
            'total_spent'     => number_format((float) $project->total_spent, 2),
            'currency'        => 'GHS',
        ];

        try {
            $this->comms->sendToModel(
                $project,
                'email',
                'construction_project_completed',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendProjectCompletionNotification failed', [
                'project_id' => $project->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
