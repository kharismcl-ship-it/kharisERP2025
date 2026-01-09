<?php

namespace Modules\Hostels\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Hostels\Models\HostelOccupantUser;

class HostelOccupantReactivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public HostelOccupantUser $hostelOccupantUser
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Hostel Portal Account Has Been Reactivated',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'hostels::emails.hostel-occupant-reactivated',
            with: [
                'hostelOccupant' => $this->hostelOccupantUser->hostelOccupant,
                'email' => $this->hostelOccupantUser->email,
                'loginUrl' => route('hostel-occupant.login'), // Adjust this route as needed
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
