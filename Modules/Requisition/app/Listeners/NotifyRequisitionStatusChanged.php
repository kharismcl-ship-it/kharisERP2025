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

        if (! $employee) {
            return;
        }

        $channels = $req->getNotificationChannels();

        $data = [
            'reference' => $req->reference,
            'status'    => ucfirst(str_replace('_', ' ', $req->status)),
            'title'     => $req->title,
            'requester' => $employee->getCommName(),
        ];

        foreach ($channels as $channel) {
            try {
                // For database (in-app) channel, skip email check
                if ($channel === 'database') {
                    $this->comms->sendFromTemplate(
                        'database',
                        'requisition_status_changed',
                        $employee->getCommEmail() ?? $employee->id,
                        $employee->getCommName(),
                        $data
                    );
                    continue;
                }

                if (! $employee->getCommEmail()) {
                    continue;
                }

                $this->comms->sendFromTemplate(
                    $channel,
                    'requisition_status_changed',
                    $employee->getCommEmail(),
                    $employee->getCommName(),
                    $data
                );
            } catch (\Throwable $e) {
                Log::warning("NotifyRequisitionStatusChanged [{$channel}] failed", [
                    'requisition_id' => $req->id,
                    'error'          => $e->getMessage(),
                ]);
            }
        }
    }
}