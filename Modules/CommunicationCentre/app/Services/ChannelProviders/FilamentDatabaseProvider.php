<?php

namespace Modules\CommunicationCentre\Services\ChannelProviders;

use App\Models\User;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Models\CommMessage;
use Modules\HR\Models\Employee;

class FilamentDatabaseProvider implements ChannelProviderInterface
{
    /**
     * Send a database notification through Filament's notification system.
     */
    public function send(CommMessage $message): void
    {
        try {
            // Find the recipient using the polymorphic relationship
            $recipient = $message->notifiable;

            if (! $recipient) {
                throw new Exception('Recipient not found for notification');
            }

            // Handle Employee models - they don't have Notifiable trait, so find their User
            if ($recipient instanceof Employee) {
                $recipient = $recipient->user;
                if (! $recipient) {
                    throw new Exception('Employee does not have an associated User account');
                }
            }

            // Send the database notification using Filament's fluent API
            Notification::make()
                ->title($message->subject)
                ->body($message->body)
                ->sendToDatabase($recipient, isEventDispatched: true);

            $message->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send Filament database notification: '.$e->getMessage());
            $message->update(['status' => 'failed']);
        }
    }
}
