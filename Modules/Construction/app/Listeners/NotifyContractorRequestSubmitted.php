<?php

namespace Modules\Construction\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Construction\Events\ContractorRequestSubmitted;

class NotifyContractorRequestSubmitted
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(ContractorRequestSubmitted $event): void
    {
        $request    = $event->request;
        $project    = $request->project;
        $contractor = $request->contractor;

        $data = [
            'request_title'   => $request->title,
            'request_type'    => ucwords(str_replace('_', ' ', $request->request_type)),
            'contractor_name' => $contractor?->name ?? 'Unknown',
            'project_name'    => $project?->name ?? 'Unknown',
            'priority'        => ucwords($request->priority),
            'description'     => $request->description,
        ];

        try {
            $this->comms->sendToModel(
                $project,
                'email',
                'construction_contractor_request_submitted',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('NotifyContractorRequestSubmitted failed', [
                'request_id' => $request->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
