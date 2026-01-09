<?php

namespace Modules\CommunicationCentre\Services\ChannelProviders;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Models\CommMessage;

class MnotifySmsProvider implements ChannelProviderInterface
{
    /**
     * Send an SMS using mNotify service.
     */
    public function send(CommMessage $message): void
    {
        try {
            // Get configuration
            $config = $message->providerConfig->config ?? [];

            // Validate required configuration
            if (empty($config['api_key']) || empty($config['sender_id'])) {
                throw new \Exception('mNotify configuration is incomplete. Missing api_key or sender_id.');
            }

            // Prepare API endpoint
            $url = 'https://api.mnotify.com/smsapi?key='.$config['api_key'];

            // Prepare payload
            $payload = [
                'to' => $message->to_phone,
                'sender_id' => $config['sender_id'],
                'message' => $message->body,
            ];

            // Send the request
            $response = Http::asForm()
                ->timeout(30)
                ->post($url, $payload);

            // Check if request was successful
            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['code']) && $responseData['code'] == '1000') {
                    // Update message with provider details
                    $message->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                        'provider_message_id' => $responseData['message_id'] ?? null,
                    ]);
                } else {
                    // Handle API error
                    throw new \Exception('mNotify API returned an error: '.($responseData['message'] ?? 'Unknown error'));
                }
            } else {
                // Handle HTTP error
                throw new \Exception('mNotify API HTTP error: '.$response->status().' - '.$response->body());
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('MnotifySmsProvider send error: '.$e->getMessage(), [
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
