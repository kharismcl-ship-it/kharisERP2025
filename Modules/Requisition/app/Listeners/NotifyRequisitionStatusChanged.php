<?php

namespace Modules\Requisition\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Requisition\Events\RequisitionStatusChanged;

class NotifyRequisitionStatusChanged
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(RequisitionStatusChanged $event): void
    {
        $req      = $event->requisition;
        $employee = $req->requesterEmployee;

        if (! $employee || ! $employee->getCommEmail()) {
            return;
        }

        $data = [
            'reference'    => $req->reference,
            'status'       => ucfirst(str_replace('_', ' ', $req->status)),
            'title'        => $req->title,
            'requester'    => $employee->getCommName(),
        ];

        try {
            $this->comms->sendFromTemplate(
                'email',
                'requisition_status_changed',
                $employee->getCommEmail(),
                $employee->getCommName(),
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('NotifyRequisitionStatusChanged failed', [
                'requisition_id' => $req->id,
                'error'          => $e->getMessage(),
            ]);
        }
    }
}
