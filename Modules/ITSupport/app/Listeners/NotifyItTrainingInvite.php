<?php

namespace Modules\ITSupport\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\ITSupport\Events\ItTrainingInviteSent;

class NotifyItTrainingInvite
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(ItTrainingInviteSent $event): void
    {
        $session = $event->session;

        foreach ($session->attendees as $attendee) {
            $employee = $attendee->employee;

            if (! $employee || ! $employee->getCommEmail()) {
                continue;
            }

            $data = [
                'title'        => $session->title,
                'scheduled_at' => $session->scheduled_at?->format('d M Y H:i'),
                'location'     => $session->location ?? 'TBD',
                'attendee'     => $employee->getCommName(),
            ];

            try {
                $this->comms->sendFromTemplate(
                    'email',
                    'it_training_invite',
                    $employee->getCommEmail(),
                    $employee->getCommName(),
                    $data
                );
            } catch (\Throwable $e) {
                Log::warning('NotifyItTrainingInvite failed', [
                    'session_id'  => $session->id,
                    'employee_id' => $employee->id,
                    'error'       => $e->getMessage(),
                ]);
            }
        }
    }
}
