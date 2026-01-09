<?php

namespace Modules\Hostels\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Hostels\Models\HostelOccupantUser;

class HostelOccupantWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public HostelOccupantUser $hostelOccupantUser,
        public string $password
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Your Hostel Portal Access',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'hostels::emails.hostel-occupant-welcome',
            with: [
                'hostelOccupant' => $this->hostelOccupantUser->hostelOccupant,
                'email' => $this->hostelOccupantUser->email,
                'password' => $this->password,
                'loginUrl' => route('hostel-occupant.login'),
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
