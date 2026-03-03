<?php

namespace Modules\Farms\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Farms\Models\FarmRequest;

class FarmRequestStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly FarmRequest $request,
        public readonly ?string $oldStatus,
    ) {}
}
