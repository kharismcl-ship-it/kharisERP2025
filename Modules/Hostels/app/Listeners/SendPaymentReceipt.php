<?php

namespace Modules\Hostels\Listeners;

use Modules\Hostels\Models\Booking;
use Modules\Hostels\Notifications\PaymentReceiptNotification;
use Modules\PaymentsChannel\Events\PaymentSucceeded;

class SendPaymentReceipt
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(PaymentSucceeded $event)
    {
        $payIntent = $event->payIntent;

        // Check if the payable is a Booking
        if ($payIntent->payable_type === Booking::class) {
            $booking = $payIntent->payable;

            if ($booking) {
                $notification = new PaymentReceiptNotification;
                $notification->send($booking, $payIntent->amount);
            }
        }
    }
}
