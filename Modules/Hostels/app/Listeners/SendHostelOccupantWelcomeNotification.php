<?php

namespace Modules\Hostels\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
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

        // TODO: Add SMS notification for phone number if needed
        // You can integrate with mNotify or other SMS services here
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
