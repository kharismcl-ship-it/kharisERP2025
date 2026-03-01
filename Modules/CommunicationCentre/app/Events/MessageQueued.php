<?php

namespace Modules\CommunicationCentre\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\CommunicationCentre\Models\CommMessage;

class MessageQueued
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public CommMessage $message
    ) {}
}
