<?php

namespace Modules\CommunicationCentre\Services\ChannelProviders;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Models\CommMessage;

class ResendEmailProvider implements ChannelProviderInterface
{
    /**
     * Send an email using the Resend API.
     */
    public function send(CommMessage $message): void
    {
        try {
            if (empty($message->to_email)) {
                throw new Exception('Recipient email is empty or null');
            }

            $apiKey = config('communicationcentre.resend.api_key');

            if (empty($apiKey)) {
                throw new Exception('Resend API key is not configured. Set RESEND_API_KEY in your .env file.');
            }

            $fromEmail = config('communicationcentre.resend.from_email', 'no-reply@example.com');
            $fromName  = config('communicationcentre.resend.from_name', 'System Notification');

            $payload = [
                'from'    => "{$fromName} <{$fromEmail}>",
                'to'      => [$message->to_email],
                'subject' => $message->subject ?? '(No Subject)',
                'text'    => $message->body,
                'html'    => nl2br(htmlspecialchars((string) $message->body, ENT_QUOTES, 'UTF-8')),
            ];

            if (! empty($message->to_name)) {
                $payload['to'] = ["{$message->to_name} <{$message->to_email}>"];
            }

            $response = Http::withToken($apiKey)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post('https://api.resend.com/emails', $payload);

            if ($response->successful()) {
                $data = $response->json();

                $message->update([
                    'status'              => 'sent',
                    'sent_at'             => now(),
                    'provider_message_id' => $data['id'] ?? null,
                ]);

                Log::info('Email sent successfully via Resend', [
                    'message_id'          => $message->id,
                    'provider_message_id' => $data['id'] ?? null,
                ]);
            } else {
                $errorBody = $response->json();
                $errorText = $errorBody['message'] ?? ($errorBody['name'] ?? $response->body());
                throw new Exception("Resend API error [{$response->status()}]: {$errorText}");
            }

        } catch (Exception $e) {
            Log::error('Resend email provider failed: '.$e->getMessage(), [
                'message_id' => $message->id ?? null,
                'to_email'   => $message->to_email,
            ]);

            $message->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
