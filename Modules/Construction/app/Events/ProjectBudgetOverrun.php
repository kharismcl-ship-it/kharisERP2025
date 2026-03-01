<?php

namespace Modules\Construction\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Construction\Models\ConstructionProject;

class ProjectBudgetOverrun
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly ConstructionProject $project,
        public readonly float $overrunAmount,
    ) {}
}