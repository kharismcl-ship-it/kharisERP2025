<?php

namespace Modules\Construction\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Construction\Models\ConstructionProject;

class ProjectCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly ConstructionProject $project) {}
}