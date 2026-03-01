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
            if (empty($config['api_key']) || empty($config['sender_id'] ?? $config['sender_sender_id'] ?? null)) {
                throw new \Exception('mNotify configuration is incomplete. Missing api_key or sender_id.');
            }

            // Prepare API endpoint
            $url = 'https://api.mnotify.com/api/sms/quick?key='.$config['api_key'];

            // Prepare JSON payload according to mNotify API documentation
            $senderId = $config['sender_id'] ?? $config['sender_sender_id'] ?? 'mNotify';
            $payload = [
                'recipient' => [$message->to_phone], // Must be an array
                'sender' => $senderId,
                'message' => $message->body,
                'is_schedule' => false,
                'schedule_date' => '',
            ];

            // Send the request with JSON content type
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->post($url, $payload);

            // Check if request was successful
            if ($response->successful()) {
                $responseData = $response->json();

                // Check for successful response according to mNotify API documentation
                if (isset($responseData['status']) && $responseData['status'] === 'success') {
                    // Update message with provider details
                    $message->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                        'provider_message_id' => $responseData['data']['message_id'] ?? null,
                    ]);
                } else {
                    // Handle API error
                    $errorMessage = $responseData['message'] ?? 'Unknown error';
                    if (isset($responseData['errors'])) {
                        $errorMessage .= ' - '.json_encode($responseData['errors']);
                    }
                    throw new \Exception('mNotify API returned an error: '.$errorMessage);
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
