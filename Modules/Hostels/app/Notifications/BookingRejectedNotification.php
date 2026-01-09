<?php

namespace Modules\Hostels\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Hostels\Models\Booking;

class BookingRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;

    public $rejectionReason;

    public function __construct(Booking $booking, string $rejectionReason)
    {
        $this->booking = $booking;
        $this->rejectionReason = $rejectionReason;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Booking Request Not Approved - '.$this->booking->booking_number)
            ->greeting('Hello '.$this->booking->guest_first_name.',')
            ->line('We regret to inform you that your booking request could not be approved.')
            ->line('Reason: '.$this->rejectionReason)
            ->line('Booking Details:')
            ->line('- Booking Number: '.$this->booking->booking_number)
            ->line('- Hostel: '.$this->booking->hostel->name)
            ->line('- Check-in: '.$this->booking->check_in_date->format('M d, Y'))
            ->line('- Check-out: '.$this->booking->check_out_date->format('M d, Y'))
            ->line('If you believe this is an error or would like to discuss alternative options, please contact our support team.')
            ->action('Contact Support', route('hostels.public.contact'))
            ->line('Thank you for your understanding.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'booking_rejected',
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'hostel_name' => $this->booking->hostel->name,
            'rejection_reason' => $this->rejectionReason,
            'message' => 'Your booking request was not approved. '.$this->rejectionReason,
            'action_url' => route('hostels.public.contact'),
        ];
    }
}
