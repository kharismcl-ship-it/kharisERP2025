<?php

namespace Modules\CommunicationCentre\Services\ChannelProviders;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Models\CommMessage;

class MailtrapEmailProvider implements ChannelProviderInterface
{
    /**
     * Mailtrap API base URL
     */
    protected string $apiBaseUrl = 'https://send.api.mailtrap.io';

    /**
     * Send an email using Mailtrap API.
     */
    public function send(CommMessage $message): void
    {
        try {
            // Check if recipient email is valid
            if (empty($message->to_email)) {
                throw new \Exception('Recipient email is empty or null');
            }

            // Get Mailtrap configuration
            $config = config('communication.mailtrap');

            if (empty($config['api_token'])) {
                throw new \Exception('Mailtrap API token is not configured');
            }

            // Prepare the API request payload
            $payload = [
                'from' => [
                    'email' => $config['from_email'] ?? 'no-reply@example.com',
                    'name' => $config['from_name'] ?? 'System Notification',
                ],
                'to' => [
                    [
                        'email' => $message->to_email,
                        'name' => $message->to_name ?? '',
                    ],
                ],
                'subject' => $message->subject,
                'text' => $message->body,
                'category' => $config['category'] ?? 'transactional',
            ];

            // Add HTML content if available
            if (! empty($message->html_body)) {
                $payload['html'] = $message->html_body;
            }

            // Make API request to Mailtrap
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$config['api_token'],
                'Content-Type' => 'application/json',
            ])->post($this->apiBaseUrl.'/api/send', $payload);

            if ($response->successful()) {
                $responseData = $response->json();

                $message->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'provider_message_id' => $responseData['message_ids'][0] ?? null,
                ]);

                Log::info('Email sent successfully via Mailtrap', [
                    'message_id' => $message->id,
                    'provider_message_id' => $responseData['message_ids'][0] ?? null,
                ]);

            } else {
                $errorMessage = $response->json()['errors'] ?? $response->body();
                throw new \Exception('Mailtrap API error: '.$errorMessage);
            }

        } catch (Exception $e) {
            Log::error('Failed to send email via Mailtrap: '.$e->getMessage(), [
                'message_id' => $message->id,
                'to_email' => $message->to_email,
            ]);

            $message->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send bulk emails using Mailtrap Batch API.
     *
     * @param  Collection|array  $messages  Collection of CommMessage objects
     * @return array Results for each message
     */
    public function sendBulk($messages): array
    {
        $results = [];

        try {
            // Convert to collection if array is provided
            if (is_array($messages)) {
                $messages = collect($messages);
            }

            // Get Mailtrap configuration
            $config = config('communication.mailtrap');

            if (empty($config['api_token'])) {
                throw new \Exception('Mailtrap API token is not configured');
            }

            // Prepare batch payload
            $batchPayload = [
                'from' => [
                    'email' => $config['from_email'] ?? 'no-reply@example.com',
                    'name' => $config['from_name'] ?? 'System Notification',
                ],
                'subject' => '', // Subject will be set per message in template variables
                'template_uuid' => $config['bulk_template_uuid'] ?? null,
                'category' => $config['category'] ?? 'bulk',
                'batch' => [],
            ];

            // Process each message for batch
            $messages->each(function (CommMessage $message, $index) use (&$batchPayload, &$results) {
                if (empty($message->to_email)) {
                    $results[$message->id] = [
                        'success' => false,
                        'error' => 'Recipient email is empty or null',
                    ];

                    return;
                }

                $batchPayload['batch'][] = [
                    'email' => $message->to_email,
                    'subject' => $message->subject,
                    'variables' => [
                        'subject' => $message->subject,
                        'body' => $message->body,
                        'to_name' => $message->to_name ?? '',
                        'message_id' => $message->id,
                    ],
                ];

                $results[$message->id] = ['success' => true];
            });

            // If using template, send as batch with template
            if (! empty($batchPayload['template_uuid'])) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$config['api_token'],
                    'Content-Type' => 'application/json',
                ])->post($this->apiBaseUrl.'/api/send/bulk', $batchPayload);
            } else {
                // Fallback: send individual emails if no template
                foreach ($messages as $message) {
                    $this->send($message);
                    $results[$message->id] = ['success' => true];
                }

                return $results;
            }

            if ($response->successful()) {
                $responseData = $response->json();

                // Update all messages with success status
                $messages->each(function (CommMessage $message) {
                    $message->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);
                });

                Log::info('Bulk email sent successfully via Mailtrap', [
                    'message_count' => $messages->count(),
                    'batch_id' => $responseData['batch_id'] ?? null,
                ]);

            } else {
                $errorMessage = $response->json()['errors'] ?? $response->body();
                throw new \Exception('Mailtrap Bulk API error: '.$errorMessage);
            }

        } catch (Exception $e) {
            Log::error('Failed to send bulk email via Mailtrap: '.$e->getMessage(), [
                'message_count' => $messages->count(),
            ]);

            // Mark all messages as failed
            foreach ($messages as $message) {
                $message->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                $results[$message->id] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Get account information from Mailtrap API.
     */
    public function getAccountInfo(): array
    {
        $config = config('communication.mailtrap');

        if (empty($config['api_token'])) {
            throw new \Exception('Mailtrap API token is not configured');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$config['api_token'],
            'Content-Type' => 'application/json',
        ])->get('https://mailtrap.io/api/accounts');

        return $response->json();
    }

    /**
     * Test Mailtrap API connection.
     */
    public function testConnection(): bool
    {
        try {
            $accountInfo = $this->getAccountInfo();

            return ! empty($accountInfo);
        } catch (Exception $e) {
            return false;
        }
    }
}
