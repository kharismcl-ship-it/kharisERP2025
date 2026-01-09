<?php

namespace Modules\Hostels\Listeners;

use Modules\CommunicationCentre\Facades\Communication;
use Modules\Hostels\Events\HostelOccupantOtpRequested;

class SendHostelOccupantOtp
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(HostelOccupantOtpRequested $event)
    {
        $hostelOccupant = $event->hostelOccupant;
        $code = $event->code;
        $message = 'Your verification code is '.$code;

        if ($hostelOccupant->phone) {
            Communication::sendRaw('sms', $hostelOccupant->phone, null, $message);
            Communication::sendRaw('whatsapp', $hostelOccupant->phone, null, $message);

            return;
        }

        if ($hostelOccupant->email) {
            Communication::sendToContact('email', $hostelOccupant->email, null, 'Your verification code', null, [
                'body' => $message,
            ]);
        }
    }
}
