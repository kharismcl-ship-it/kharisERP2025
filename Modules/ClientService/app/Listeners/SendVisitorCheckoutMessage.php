<?php

namespace Modules\ClientService\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\ClientService\Events\VisitorCheckedOut;
use Modules\CommunicationCentre\Services\CommunicationService;

class SendVisitorCheckoutMessage
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(VisitorCheckedOut $event): void
    {
        $visitor = $event->visitor;

        if (! $visitor->phone && ! $visitor->email) {
            return;
        }

        $data = [
            'visitor_name' => $visitor->full_name,
            'company_name' => optional($visitor->company)->name ?? 'our company',
        ];

        $channel   = $visitor->phone ? 'sms' : 'email';
        $recipient = $visitor->phone ?? $visitor->email;
        $name      = $visitor->full_name;

        try {
            $this->comms->sendFromTemplate(
                $channel,
                'visitor_checkout_message',
                $recipient,
                $name,
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendVisitorCheckoutMessage failed', [
                'visitor_id' => $visitor->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
