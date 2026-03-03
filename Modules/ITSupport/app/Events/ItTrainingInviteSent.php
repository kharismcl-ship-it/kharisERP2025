<?php

namespace Modules\ITSupport\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\ITSupport\Models\ItTrainingSession;

class ItTrainingInviteSent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly ItTrainingSession $session,
    ) {}
}
