<?php

namespace Modules\ITSupport\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\ITSupport\Events\ItRequestStatusChanged;

class NotifyItRequestStatusChanged
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(ItRequestStatusChanged $event): void
    {
        $request  = $event->request;
        $employee = $request->requesterEmployee;

        if (! $employee || ! $employee->getCommEmail()) {
            return;
        }

        $data = [
            'reference' => $request->reference,
            'subject'   => $request->subject,
            'status'    => ucfirst(str_replace('_', ' ', $request->status)),
            'requester' => $employee->getCommName(),
        ];

        try {
            $this->comms->sendFromTemplate(
                'email',
                'it_request_status_changed',
                $employee->getCommEmail(),
                $employee->getCommName(),
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('NotifyItRequestStatusChanged failed', [
                'request_id' => $request->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
