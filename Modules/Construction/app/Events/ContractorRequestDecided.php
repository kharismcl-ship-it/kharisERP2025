<?php

namespace Modules\Construction\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Construction\Models\ContractorRequest;

class ContractorRequestDecided
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly ContractorRequest $request,
        public readonly string $decision
    ) {}
}
