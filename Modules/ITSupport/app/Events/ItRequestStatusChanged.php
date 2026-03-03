<?php

namespace Modules\ITSupport\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\ITSupport\Models\ItRequest;

class ItRequestStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly ItRequest $request,
        public readonly string $oldStatus,
    ) {}
}
