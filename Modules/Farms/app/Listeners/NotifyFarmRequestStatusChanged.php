<?php

namespace Modules\Farms\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Farms\Events\FarmRequestStatusChanged;

class NotifyFarmRequestStatusChanged
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(FarmRequestStatusChanged $event): void
    {
        $request   = $event->request;
        $newStatus = $request->status;

        if (! in_array($newStatus, ['approved', 'rejected'])) {
            return;
        }

        // Get requesting worker's email via HR employee link
        $workerEmail = $request->requestedBy?->employee?->email ?? null;

        if (! $workerEmail) {
            return;
        }

        $templateCode = $newStatus === 'approved'
            ? 'farms_request_approved'
            : 'farms_request_rejected';

        $data = [
            'farm_name'        => $request->farm?->name ?? 'Farm',
            'reference'        => $request->reference,
            'title'            => $request->title,
            'request_type'     => ucfirst($request->request_type),
            'urgency'          => ucfirst($request->urgency),
            'status'           => ucfirst($newStatus),
            'rejection_reason' => $request->rejection_reason ?? '',
            'notes'            => $request->notes ?? '',
        ];

        try {
            $this->comms->sendToContact(
                'email',
                $workerEmail,
                null,
                null,
                $templateCode,
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('NotifyFarmRequestStatusChanged failed', [
                'farm_request_id' => $request->id,
                'error'           => $e->getMessage(),
            ]);
        }
    }
}
