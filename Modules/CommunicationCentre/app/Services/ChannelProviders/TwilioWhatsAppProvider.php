<?php

namespace Modules\CommunicationCentre\Services\ChannelProviders;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Models\CommMessage;
use Twilio\Rest\Client;

class TwilioWhatsAppProvider implements ChannelProviderInterface
{
    /**
     * Send a WhatsApp message using Twilio service.
     */
    public function send(CommMessage $message): void
    {
        try {
            // Get configuration
            $config = $message->providerConfig->config ?? [];

            // Validate required configuration
            if (empty($config['account_sid']) || empty($config['auth_token']) || empty($config['from_number'])) {
                throw new \Exception('Twilio configuration is incomplete. Missing account_sid, auth_token, or from_number.');
            }

            // Create Twilio client
            $client = new Client($config['account_sid'], $config['auth_token']);

            // Format phone numbers
            $to = 'whatsapp:'.$message->to_phone;
            $from = 'whatsapp:'.$config['from_number'];

            // Send message
            $twilioMessage = $client->messages->create(
                $to,
                [
                    'from' => $from,
                    'body' => $message->body,
                ]
            );

            // Update message with provider details
            $message->update([
                'status' => 'sent',
                'sent_at' => now(),
                'provider_message_id' => $twilioMessage->sid,
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('TwilioWhatsAppProvider send error: '.$e->getMessage(), [
                'message_id' => $message->id,
                'to_phone' => $message->to_phone,
                'exception' => $e,
            ]);

            // Update message with error details
            $message->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
