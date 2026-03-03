<?php

namespace Modules\Construction\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Construction\Models\ConstructionWorker;
use Modules\Construction\Models\WorkerAttendance;

class WorkerCheckedOut
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly ConstructionWorker $worker,
        public readonly WorkerAttendance $attendance
    ) {}
}
