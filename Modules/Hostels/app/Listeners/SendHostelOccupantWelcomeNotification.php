<?php

namespace Modules\Hostels\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Hostels\Events\HostelOccupantUserCreated;
use Modules\Hostels\Mail\HostelOccupantWelcomeMail;

class SendHostelOccupantWelcomeNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(HostelOccupantUserCreated $event): void
    {
        // Send welcome email with credentials
        if ($event->hostelOccupantUser->email) {
            Mail::to($event->hostelOccupantUser->email)
                ->send(new HostelOccupantWelcomeMail($event->hostelOccupantUser, $event->password));
        }

        // Send SMS notification via CommunicationCentre
        $occupant = $event->hostelOccupantUser->hostelOccupant;
        if ($occupant && $occupant->phone) {
            try {
                app(CommunicationService::class)->sendToContact(
                    channel: 'sms',
                    toEmail: null,
                    toPhone: $occupant->phone,
                    subject: null,
                    templateCode: 'hostel_occupant_welcome_sms',
                    data: [
                        'name'  => $occupant->full_name ?? $occupant->first_name,
                        'email' => $event->hostelOccupantUser->email,
                    ]
                );
            } catch (\Exception $e) {
                Log::warning('Failed to send welcome SMS to hostel occupant', [
                    'hostel_occupant_id' => $occupant->id,
                    'phone'              => $occupant->phone,
                    'error'              => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(HostelOccupantUserCreated $event, \Throwable $exception): void
    {
        // Log the failure but don't prevent the check-in process
        \Illuminate\Support\Facades\Log::error('Failed to send hostel occupant welcome notification', [
            'hostel_occupant_user_id' => $event->hostelOccupantUser->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
