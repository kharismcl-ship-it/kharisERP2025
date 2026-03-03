<?php

namespace Modules\ClientService\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\ClientService\Models\CsVisitor;

class VisitorCheckedOut
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly CsVisitor $visitor,
    ) {}
}
