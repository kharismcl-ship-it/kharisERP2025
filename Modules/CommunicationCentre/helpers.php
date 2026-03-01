<?php

use Modules\CommunicationCentre\Services\CommunicationHelper;

if (! function_exists('comm')) {
    /**
     * Get the CommunicationHelper instance or call a method on it.
     */
    function comm(?string $method = null, ...$args)
    {
        $helper = app(CommunicationHelper::class);

        if ($method === null) {
            return $helper;
        }

        return $helper->$method(...$args);
    }
}

if (! function_exists('send_template')) {
    /**
     * Send a message using a template.
     */
    function send_template(string $templateCode, $recipient, array $data = [], ?string $channel = null)
    {
        return comm('sendTemplate', $templateCode, $recipient, $data, $channel);
    }
}

if (! function_exists('send_custom')) {
    /**
     * Send a custom message.
     */
    function send_custom(string $subject, string $body, $recipient, string $channel = 'email', ?string $provider = null)
    {
        return comm('sendCustom', $subject, $body, $recipient, $channel, $provider);
    }
}

if (! function_exists('send_email')) {
    /**
     * Send an email quickly.
     */
    function send_email(string $to, string $subject, string $body, ?string $templateCode = null)
    {
        return comm('email', $to, $subject, $body, $templateCode);
    }
}

if (! function_exists('send_sms')) {
    /**
     * Send an SMS quickly.
     */
    function send_sms(string $to, string $message, ?string $templateCode = null)
    {
        return comm('sms', $to, $message, $templateCode);
    }
}

if (! function_exists('get_message_status')) {
    /**
     * Get message delivery status.
     */
    function get_message_status(string $messageId): ?array
    {
        return comm('getMessageStatus', $messageId);
    }
}

if (! function_exists('has_opted_in')) {
    /**
     * Check if a user has opted in for a channel.
     */
    function has_opted_in($user, string $channel): bool
    {
        return comm('hasOptedIn', $user, $channel);
    }
}
