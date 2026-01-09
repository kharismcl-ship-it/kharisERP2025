<?php

namespace Modules\Hostels\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Hostels\Models\Booking;

class BookingApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Booking Approved - '.$this->booking->booking_number)
            ->greeting('Hello '.$this->booking->guest_first_name.',')
            ->line('Your booking request has been approved!')
            ->line('Booking Details:')
            ->line('- Booking Number: '.$this->booking->booking_number)
            ->line('- Hostel: '.$this->booking->hostel->name)
            ->line('- Bed: '.($this->booking->bed ? $this->booking->bed->name : 'Not assigned'))
            ->line('- Check-in: '.$this->booking->check_in_date->format('M d, Y'))
            ->line('- Check-out: '.$this->booking->check_out_date->format('M d, Y'))
            ->line('Please proceed to make payment to confirm your booking.')
            ->action('Make Payment', route('hostels.public.booking.payment', $this->booking))
            ->line('Thank you for choosing our hostel!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'booking_approved',
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'hostel_name' => $this->booking->hostel->name,
            'message' => 'Your booking request has been approved. Please proceed with payment.',
            'action_url' => route('hostels.public.booking.payment', $this->booking),
        ];
    }
}
