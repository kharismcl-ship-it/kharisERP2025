<?php

namespace Modules\Construction\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Construction\Models\ProjectPhase;

class ProjectPhaseApproved
{
    use SerializesModels;

    /**
     * @param  array<array{description: string, quantity: float, unit_price: float}>  $materials
     */
    public function __construct(
        public ProjectPhase $projectPhase,
        public array $materials = []
    ) {}
}
