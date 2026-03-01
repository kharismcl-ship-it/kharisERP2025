<?php

namespace Modules\CommunicationCentre\Contracts;

interface CommunicationNotification
{
    /**
     * Get the communication representation of the notification.
     *
     * Expected return format:
     * [
     *     'template_code' => 'template_identifier', // Optional: use template
     *     'subject' => 'Message Subject',          // Required if no template_code
     *     'body' => 'Message Body',                // Required if no template_code
     *     'channel' => 'email',                    // Optional: defaults to 'email'
     *     'provider' => 'laravel_mail',            // Optional: provider override
     *     'data' => []                            // Optional: template variables
     * ]
     */
    public function toCommunication($notifiable): array;
}
