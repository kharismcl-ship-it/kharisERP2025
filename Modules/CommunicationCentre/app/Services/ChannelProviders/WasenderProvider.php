<?php

namespace Modules\CommunicationCentre\Services\ChannelProviders;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Models\CommMessage;

class WasenderProvider implements ChannelProviderInterface
{
    /**
     * Send a WhatsApp message using Wasender service.
     */
    public function send(CommMessage $message): void
    {
        try {
            // Get configuration
            $config = $message->providerConfig->config ?? [];

            // Validate required configuration
            if (empty($config['base_url']) || empty($config['token']) || empty($config['device_id'])) {
                throw new \Exception('Wasender configuration is incomplete. Missing base_url, token, or device_id.');
            }

            // Prepare API endpoint
            $baseUrl = rtrim($config['base_url'], '/');
            $url = $baseUrl.'/api/send-message';

            // Prepare headers
            $headers = [
                'Authorization' => 'Bearer '.$config['token'],
                'Content-Type' => 'application/json',
            ];

            // Prepare payload
            $payload = [
                'to' => $message->to_phone,
                'device' => $config['device_id'],
            ];

            // Check message type and prepare appropriate payload
            $this->preparePayloadByMessageType($payload, $message);

            // Add subject if available (for document messages)
            if (! empty($message->subject)) {
                $payload['fileName'] = $message->subject;
            }

            // Send the request
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($url, $payload);

            // Check if request was successful
            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['success']) && $responseData['success']) {
                    // Update message with provider details
                    $message->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                        'provider_message_id' => $responseData['data']['msgId'] ?? null,
                    ]);
                } else {
                    // Handle API error
                    throw new \Exception('Wasender API returned an error: '.($responseData['message'] ?? 'Unknown error'));
                }
            } else {
                // Handle HTTP error
                throw new \Exception('Wasender API HTTP error: '.$response->status().' - '.$response->body());
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('WasenderProvider send error: '.$e->getMessage(), [
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

    /**
     * Prepare payload based on message type
     */
    protected function preparePayloadByMessageType(array &$payload, CommMessage $message): void
    {
        $mediaUrl = $message->metadata['media_url'] ?? null;

        if ($mediaUrl) {
            $mediaType = $this->getMediaType($mediaUrl);

            switch ($mediaType) {
                case 'image':
                    $payload['imageUrl'] = $mediaUrl;
                    break;
                case 'video':
                    $payload['videoUrl'] = $mediaUrl;
                    break;
                case 'document':
                    $payload['documentUrl'] = $mediaUrl;
                    break;
                default:
                    // Log unsupported media type
                    Log::warning('WasenderProvider: Unsupported media type for URL: '.$mediaUrl, ['message_id' => $message->id]);
                    break;
            }
        }

        // Always include the body as the text part of the message
        if (! empty($message->body)) {
            $payload['text'] = $message->body;
        }
    }

    /**
     * Extract media URL from message if present
     */
    protected function extractMediaUrl(CommMessage $message): ?string
    {
        // DEPRECATED: This logic is moved to check metadata['media_url'] directly.
        return null;
    }

    /**
     * Determine media type based on URL
     */
    protected function getMediaType(string $url): string
    {
        $pathInfo = pathinfo($url);
        $extension = strtolower($pathInfo['extension'] ?? '');

        // Image extensions
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        if (in_array($extension, $imageExtensions)) {
            return 'image';
        }

        // Video extensions
        $videoExtensions = ['mp4', '3gpp', 'mov', 'avi', 'mkv'];
        if (in_array($extension, $videoExtensions)) {
            return 'video';
        }

        // Document extensions
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
        if (in_array($extension, $documentExtensions)) {
            return 'document';
        }

        return 'unknown';
    }
}
