<?php

namespace Modules\Requisition\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Requisition\Events\RequisitionPartyAdded;

class NotifyRequisitionPartyAdded
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(RequisitionPartyAdded $event): void
    {
        $requisition = $event->requisition;
        $party       = $event->party;
        $requester   = $requisition->requesterEmployee;

        $recipients = $party->resolveRecipients();

        foreach ($recipients as $employee) {
            if (! $employee->getCommEmail()) {
                continue;
            }

            try {
                $this->comms->sendFromTemplate(
                    'email',
                    'requisition_party_notified',
                    $employee->getCommEmail(),
                    $employee->getCommName(),
                    [
                        'reference' => $requisition->reference,
                        'title'     => $requisition->title,
                        'reason'    => \Modules\Requisition\Models\RequisitionParty::REASONS[$party->reason] ?? $party->reason,
                        'requester' => $requester?->getCommName() ?? 'System',
                    ]
                );

                $party->update(['notified_at' => now()]);
            } catch (\Throwable $e) {
                Log::warning('RequisitionPartyAdded notification failed', [
                    'requisition_id' => $requisition->id,
                    'employee_id'    => $employee->id,
                    'error'          => $e->getMessage(),
                ]);
            }
        }
    }
}