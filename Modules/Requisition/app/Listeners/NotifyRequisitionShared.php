<?php

namespace Modules\Requisition\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Requisition\Events\RequisitionShared;

class NotifyRequisitionShared
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(RequisitionShared $event): void
    {
        $req      = $event->requisition;
        $approver = $event->approver;
        $employee = $approver->employee;

        if (! $employee || ! $employee->getCommEmail()) {
            return;
        }

        $data = [
            'reference' => $req->reference,
            'title'     => $req->title,
            'role'      => ucfirst($approver->role),
            'requester' => optional($req->requesterEmployee)->getCommName(),
        ];

        try {
            $this->comms->sendFromTemplate(
                'email',
                'requisition_shared_with_you',
                $employee->getCommEmail(),
                $employee->getCommName(),
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('NotifyRequisitionShared failed', [
                'requisition_id' => $req->id,
                'approver_id'    => $approver->id,
                'error'          => $e->getMessage(),
            ]);
        }
    }
}
