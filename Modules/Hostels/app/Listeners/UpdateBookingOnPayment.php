<?php

namespace Modules\Hostels\Listeners;

use Modules\Hostels\Events\BookingConfirmed;
use Modules\Hostels\Events\BookingPaymentCompleted;
use Modules\Hostels\Models\Booking;
use Modules\PaymentsChannel\Events\PaymentFailed;
use Modules\PaymentsChannel\Events\PaymentSucceeded;

class UpdateBookingOnPayment
{
    /**
     * Handle the event.
     *
     * @param  PaymentSucceeded|PaymentFailed  $event
     * @return void
     */
    public function __invoke($event)
    {
        if ($event instanceof PaymentSucceeded) {
            $this->handlePaymentSucceeded($event);
        } elseif ($event instanceof PaymentFailed) {
            $this->handlePaymentFailed($event);
        }
    }

    /**
     * Handle the event when a payment is successful.
     *
     * @return void
     */
    protected function handlePaymentSucceeded(PaymentSucceeded $event)
    {
        $payIntent = $event->payIntent;

        // Check if the payable is a Booking
        if ($payIntent->payable_type === Booking::class) {
            $booking = $payIntent->payable;

            if ($booking && $booking->payment_status !== 'paid') {
                // Check if booking hold has expired
                if ($booking->isHoldExpired()) {
                    // Release the bed and cancel the booking
                    $booking->releaseBedIfHoldExpired();

                    return; // Exit early, don't process payment for expired booking
                }

                // Update booking status to confirmed and payment status to paid
                $booking->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'amount_paid' => $booking->total_amount,
                    'balance_amount' => 0,
                ]);

                // Update bed status if selected
                if ($booking->bed_id) {
                    $booking->bed->update(['status' => 'occupied']);
                }

                // Fire booking events
                event(new BookingConfirmed($booking));
                event(new BookingPaymentCompleted($booking));
            }
        }
    }

    /**
     * Handle the event when a payment fails.
     *
     * @return void
     */
    protected function handlePaymentFailed(PaymentFailed $event)
    {
        $payIntent = $event->payIntent;

        // Check if the payable is a Booking
        if ($payIntent->payable_type === Booking::class) {
            $booking = $payIntent->payable;

            if ($booking && $booking->payment_status !== 'paid') {
                // Update booking payment status to failed
                $booking->update([
                    'payment_status' => 'failed',
                ]);
            }
        }
    }
}
