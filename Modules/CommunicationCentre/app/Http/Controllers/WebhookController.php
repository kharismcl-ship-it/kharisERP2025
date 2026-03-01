<?php

namespace Modules\CommunicationCentre\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\CommunicationCentre\Events\MessageDelivered;
use Modules\CommunicationCentre\Events\MessageFailed;
use Modules\CommunicationCentre\Models\CommMessage;

class WebhookController extends Controller
{
    /**
     * Handle incoming webhooks from communication providers
     */
    public function handle(Request $request, string $provider): JsonResponse
    {
        // Validate provider
        $validProviders = ['mailtrap', 'mnotify', 'wasender', 'twilio'];
        if (! in_array($provider, $validProviders)) {
            return response()->json(['error' => 'Invalid provider'], 400);
        }

        // Process based on provider
        $result = match ($provider) {
            'mailtrap' => $this->handleMailtrapWebhook($request),
            'mnotify' => $this->handleMnotifyWebhook($request),
            'wasender' => $this->handleWasenderWebhook($request),
            'twilio' => $this->handleTwilioWebhook($request),
            default => ['error' => 'Provider not implemented']
        };

        if (isset($result['error'])) {
            return response()->json($result, 400);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle Mailtrap email delivery webhook
     */
    protected function handleMailtrapWebhook(Request $request): array
    {
        $data = $request->validate([
            'message_id' => 'required|string',
            'event' => 'required|string|in:delivered,failed',
            'email' => 'required|email',
            'timestamp' => 'required|numeric',
        ]);

        // Find message by provider message ID
        $message = CommMessage::where('provider_message_id', $data['message_id'])
            ->where('to_email', $data['email'])
            ->first();

        if (! $message) {
            return ['error' => 'Message not found'];
        }

        $this->updateMessageStatus($message, $data['event'], $data);

        return ['success' => true];
    }

    /**
     * Handle Mnotify SMS delivery webhook
     */
    protected function handleMnotifyWebhook(Request $request): array
    {
        $data = $request->validate([
            'message_id' => 'required|string',
            'status' => 'required|string|in:delivered,failed',
            'phone' => 'required|string',
            'reason' => 'nullable|string',
        ]);

        $message = CommMessage::where('provider_message_id', $data['message_id'])
            ->where('to_phone', $data['phone'])
            ->first();

        if (! $message) {
            return ['error' => 'Message not found'];
        }

        $this->updateMessageStatus($message, $data['status'], $data);

        return ['success' => true];
    }

    /**
     * Handle Wasender WhatsApp webhook
     */
    protected function handleWasenderWebhook(Request $request): array
    {
        $data = $request->validate([
            'message_id' => 'required|string',
            'status' => 'required|string|in:sent,delivered,read,failed',
            'phone' => 'required|string',
            'error' => 'nullable|string',
        ]);

        $message = CommMessage::where('provider_message_id', $data['message_id'])
            ->where('to_phone', $data['phone'])
            ->first();

        if (! $message) {
            return ['error' => 'Message not found'];
        }

        $this->updateMessageStatus($message, $data['status'], $data);

        return ['success' => true];
    }

    /**
     * Handle Twilio webhook
     */
    protected function handleTwilioWebhook(Request $request): array
    {
        $data = $request->validate([
            'MessageSid' => 'required|string',
            'MessageStatus' => 'required|string|in:queued,sent,delivered,failed',
            'To' => 'required|string',
            'ErrorCode' => 'nullable|string',
            'ErrorMessage' => 'nullable|string',
        ]);

        $message = CommMessage::where('provider_message_id', $data['MessageSid'])
            ->where('to_phone', $data['To'])
            ->first();

        if (! $message) {
            return ['error' => 'Message not found'];
        }

        $this->updateMessageStatus($message, $data['MessageStatus'], $data);

        return ['success' => true];
    }

    /**
     * Update message status and fire appropriate events
     */
    protected function updateMessageStatus(CommMessage $message, string $status, array $data): void
    {
        $updates = [];
        $event = null;

        switch ($status) {
            case 'delivered':
            case 'read':
                $updates = [
                    'status' => 'delivered',
                    'delivered_at' => now(),
                    'error_message' => null,
                ];
                $event = new MessageDelivered($message);
                break;

            case 'sent':
                $updates = [
                    'status' => 'sent',
                    'sent_at' => now(),
                    'error_message' => null,
                ];
                break;

            case 'failed':
                $updates = [
                    'status' => 'failed',
                    'error_message' => $data['error'] ?? $data['ErrorMessage'] ?? $data['reason'] ?? 'Delivery failed',
                ];
                $event = new MessageFailed($message, $updates['error_message']);
                break;
        }

        if (! empty($updates)) {
            $message->update($updates);
        }

        if ($event) {
            event($event);
        }
    }
}
