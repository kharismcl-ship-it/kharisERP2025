<?php

namespace Modules\Construction\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Construction\Events\ContractorRequestDecided;

class NotifyContractorRequestDecision
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(ContractorRequestDecided $event): void
    {
        $request    = $event->request;
        $decision   = $event->decision;
        $contractor = $request->contractor;
        $project    = $request->project;

        $data = [
            'request_title'    => $request->title,
            'request_type'     => ucwords(str_replace('_', ' ', $request->request_type)),
            'contractor_name'  => $contractor?->name ?? 'Unknown',
            'project_name'     => $project?->name ?? 'Unknown',
            'approved_amount'  => $request->approved_amount ? 'GHS ' . number_format((float) $request->approved_amount, 2) : 'N/A',
            'approval_notes'   => $request->approval_notes ?? '',
        ];

        $emailTemplate = $decision === 'approved'
            ? 'construction_contractor_request_approved'
            : 'construction_contractor_request_rejected';

        try {
            $this->comms->sendToModel(
                $request,
                'email',
                $emailTemplate,
                $data
            );

            if ($decision === 'approved') {
                $this->comms->sendToModel(
                    $request,
                    'sms',
                    'construction_contractor_request_approved_sms',
                    $data
                );
            }
        } catch (\Throwable $e) {
            Log::warning('NotifyContractorRequestDecision failed', [
                'request_id' => $request->id,
                'decision'   => $decision,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
