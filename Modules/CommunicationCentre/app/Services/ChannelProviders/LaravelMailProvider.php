<?php

namespace Modules\CommunicationCentre\Services\ChannelProviders;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\CommunicationCentre\Models\CommMessage;

class LaravelMailProvider implements ChannelProviderInterface
{
    /**
     * Send an email using Laravel's mail system.
     */
    public function send(CommMessage $message): void
    {
        try {
            Mail::raw($message->content, function ($mail) use ($message) {
                $mail->to($message->recipient)
                    ->subject($message->subject);
            });

            $message->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send email: '.$e->getMessage());
            $message->update(['status' => 'failed']);
        }
    }
}
