<?php

namespace Modules\Hostels\Services;

use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\MaintenanceRequest;
use Modules\Hostels\Notifications\BookingConfirmationNotification;
use Modules\Hostels\Notifications\CheckInNotification;
use Modules\Hostels\Notifications\CheckoutReminderNotification;
use Modules\Hostels\Notifications\MaintenanceRequestNotification;
use Modules\Hostels\Notifications\PaymentReceiptNotification;
use Modules\Hostels\Notifications\PreArrivalNotification;

class HostelCommunicationService
{
    /**
     * Send booking confirmation to hostel occupant
     *
     * @return void
     */
    public function sendBookingConfirmation(Booking $booking)
    {
        $notification = new BookingConfirmationNotification;
        $notification->send($booking);
    }

    /**
     * Send check-in notification to hostel occupant
     *
     * @return void
     */
    public function sendCheckInNotification(Booking $booking)
    {
        $notification = new CheckInNotification;
        $notification->send($booking);
    }

    /**
     * Send payment receipt to hostel occupant
     *
     * @return void
     */
    public function sendPaymentReceipt(Booking $booking, float $amount)
    {
        $notification = new PaymentReceiptNotification;
        $notification->send($booking, $amount);
    }

    /**
     * Send maintenance request notification to staff
     *
     * @return void
     */
    public function sendMaintenanceRequestNotification(MaintenanceRequest $request)
    {
        $notification = new MaintenanceRequestNotification;
        $notification->send($request);
    }

    /**
     * Send checkout reminder to tenant
     *
     * @return void
     */
    public function sendCheckoutReminder(Booking $booking)
    {
        $notification = new CheckoutReminderNotification;
        $notification->send($booking);
    }

    /**
     * Send pre-arrival welcome notification to tenant
     * Includes important information about the stay
     *
     * @return void
     */
    public function sendPreArrivalWelcome(Booking $booking)
    {
        $notification = new PreArrivalNotification;
        $notification->sendWelcome($booking);
    }

    /**
     * Send pre-arrival reminder notification (3 days before check-in)
     *
     * @return void
     */
    public function sendPreArrivalReminder(Booking $booking)
    {
        $notification = new PreArrivalNotification;
        $notification->sendReminder($booking);
    }

    /**
     * Send final pre-arrival reminder notification (1 day before check-in)
     *
     * @return void
     */
    public function sendPreArrivalFinalReminder(Booking $booking)
    {
        $notification = new PreArrivalNotification;
        $notification->sendFinalReminder($booking);
    }
}
