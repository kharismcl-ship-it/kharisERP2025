<?php

namespace Modules\CommunicationCentre\Services;

use Illuminate\Database\Eloquent\Model;
use Modules\CommunicationCentre\Models\CommMessage;
use Modules\CommunicationCentre\Models\CommPreference;
use Modules\CommunicationCentre\Models\CommTemplate;

class CommunicationHelper
{
    protected CommunicationService $communicationService;

    public function __construct(CommunicationService $communicationService)
    {
        $this->communicationService = $communicationService;
    }

    /**
     * Send a message using a template code.
     */
    public function sendTemplate(string $templateCode, Model $recipient, array $data = [], ?string $channel = null): mixed
    {
        $template = CommTemplate::where('code', $templateCode)
            ->where('is_active', true)
            ->first();

        if (! $template) {
            throw new \Exception("Template with code '{$templateCode}' not found or inactive");
        }

        $channel = $channel ?? $template->channel;

        $result = $this->communicationService->sendToModel(
            $recipient,
            $channel,
            $templateCode,
            $data
        );

        return $result instanceof CommMessage ? $result : null;
    }

    /**
     * Send a custom message without a template.
     */
    public function sendCustom(string $subject, string $body, Model $recipient, string $channel = 'email', ?string $provider = null): mixed
    {
        $data = ['subject' => $subject, 'body' => $body];

        $result = // For custom messages without template, use sendRaw
        $toEmail = $recipient->email ?? null;
        $toPhone = $recipient->phone ?? null;

        return $this->communicationService->sendRaw(
            $channel,
            $toPhone,
            $subject,
            $body
        );

        return $result instanceof CommMessage ? $result : null;
    }

    /**
     * Send a message to multiple recipients.
     */
    public function sendBulk(string $templateCode, array $recipients, array $data = [], ?string $channel = null): array
    {
        $results = [];

        foreach ($recipients as $recipient) {
            try {
                $results[] = $this->sendTemplate($templateCode, $recipient, $data, $channel);
            } catch (\Exception $e) {
                $results[] = ['error' => $e->getMessage(), 'recipient' => $recipient->getKey()];
            }
        }

        return $results;
    }

    /**
     * Get user communication preferences.
     */
    public function getUserPreferences(Model $user): array
    {
        return CommPreference::where('preferenceable_type', get_class($user))
            ->where('preferenceable_id', $user->getKey())
            ->get()
            ->pluck('value', 'channel')
            ->toArray();
    }

    /**
     * Update user communication preferences.
     */
    public function updateUserPreferences(Model $user, array $preferences): void
    {
        foreach ($preferences as $channel => $value) {
            CommPreference::updateOrCreate(
                [
                    'preferenceable_type' => get_class($user),
                    'preferenceable_id' => $user->getKey(),
                    'channel' => $channel,
                ],
                ['value' => $value]
            );
        }
    }

    /**
     * Check if a user has opted in for a channel.
     */
    public function hasOptedIn(Model $user, string $channel): bool
    {
        $preference = CommPreference::where('preferenceable_type', get_class($user))
            ->where('preferenceable_id', $user->getKey())
            ->where('channel', $channel)
            ->first();

        return $preference && $preference->value === true;
    }

    /**
     * Get message delivery status.
     */
    public function getMessageStatus(string $messageId): ?array
    {
        $message = CommMessage::find($messageId);

        if (! $message) {
            return null;
        }

        return [
            'status' => $message->status,
            'delivered_at' => $message->delivered_at,
            'read_at' => $message->read_at,
            'error_message' => $message->error_message,
            'provider_response' => $message->provider_response,
        ];
    }

    /**
     * Get messages for a recipient.
     */
    public function getRecipientMessages(Model $recipient, int $limit = 10): array
    {
        return CommMessage::where('recipient_type', get_class($recipient))
            ->where('recipient_id', $recipient->getKey())
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Quick email sending shortcut.
     */
    public function email(string $to, string $subject, string $body, ?string $templateCode = null): ?CommMessage
    {
        // Create a temporary user model for sending
        $recipient = new class($to) extends Model
        {
            protected $email;

            public function __construct($email)
            {
                $this->email = $email;
            }

            public function getEmailAttribute()
            {
                return $this->email;
            }
        };

        if ($templateCode) {
            return $this->sendTemplate($templateCode, $recipient, ['email' => $to, 'subject' => $subject, 'body' => $body], 'email');
        }

        return $this->sendCustom($subject, $body, $recipient, 'email');
    }

    /**
     * Quick SMS sending shortcut.
     */
    public function sms(string $to, string $message, ?string $templateCode = null): ?CommMessage
    {
        // Create a temporary user model for sending
        $recipient = new class($to) extends Model
        {
            protected $phone;

            public function __construct($phone)
            {
                $this->phone = $phone;
            }

            public function getPhoneAttribute()
            {
                return $this->phone;
            }
        };

        if ($templateCode) {
            return $this->sendTemplate($templateCode, $recipient, ['phone' => $to, 'message' => $message], 'sms');
        }

        return $this->sendCustom('SMS', $message, $recipient, 'sms');
    }

    /**
     * Check if communication is enabled for a channel.
     */
    public function isChannelEnabled(string $channel): bool
    {
        $config = config('communicationcentre.providers', []);

        foreach ($config as $provider => $settings) {
            if (($settings['channels'][$channel] ?? false) && ! empty($settings['api_key'])) {
                return true;
            }
        }

        return false;
    }
}
