<?php

namespace Modules\Construction\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Construction\Models\ConstructionProject;
use Modules\Construction\Models\ProjectPhase;

class ProjectMilestoneCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly ConstructionProject $project,
        public readonly ProjectPhase $phase,
    ) {}
}
