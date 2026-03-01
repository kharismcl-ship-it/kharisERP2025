<?php

namespace Modules\CommunicationCentre\Channels;

use Illuminate\Notifications\Notification;
use Modules\CommunicationCentre\Contracts\CommunicationNotification;
use Modules\CommunicationCentre\Services\CommunicationHelper;

class CommunicationChannel
{
    protected CommunicationHelper $communicationHelper;

    public function __construct(CommunicationHelper $communicationHelper)
    {
        $this->communicationHelper = $communicationHelper;
    }

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification): void
    {
        // Check if notification implements the CommunicationNotification interface
        if (! $notification instanceof CommunicationNotification) {
            throw new \InvalidArgumentException(
                'Notification class "'.get_class($notification).'" must implement CommunicationNotification interface. '.
                'Expected method: public function toCommunication($notifiable): array'
            );
        }

        $message = $notification->toCommunication($notifiable);

        if (empty($message['template_code']) && (empty($message['subject']) || empty($message['body']))) {
            throw new \InvalidArgumentException('Notification must provide either template_code or both subject and body');
        }

        $channel = $message['channel'] ?? 'email';
        $data = $message['data'] ?? [];

        if (! empty($message['template_code'])) {
            // Send using template
            $this->communicationHelper->sendTemplate(
                $message['template_code'],
                $notifiable,
                $data,
                $channel
            );
        } else {
            // Send custom message
            $this->communicationHelper->sendCustom(
                $message['subject'],
                $message['body'],
                $notifiable,
                $channel,
                $message['provider'] ?? null
            );
        }
    }
}
