<?php

namespace Modules\Construction\Services;

use Illuminate\Support\Facades\DB;
use Modules\Construction\Models\ConstructionProject;
use Modules\Construction\Models\MaterialUsage;
use Modules\Construction\Models\ProjectPhase;
use Modules\Construction\Models\ProjectTask;

class ConstructionService
{
    /**
     * Recalculate and persist project total_spent from all material usages and budget items.
     */
    public function recalculateProjectSpend(ConstructionProject $project): void
    {
        $materialTotal = $project->materialUsages()->sum('total_cost');
        $budgetActual  = $project->budgetItems()->sum('actual_amount');

        $project->update(['total_spent' => max($materialTotal, $budgetActual)]);
    }

    /**
     * Recalculate phase spent from its material usages.
     */
    public function recalculatePhaseSpend(ProjectPhase $phase): void
    {
        $spent = $phase->materialUsages()->sum('total_cost');
        $phase->update(['spent' => $spent]);
    }

    /**
     * Update phase progress based on completed tasks ratio.
     */
    public function updatePhaseProgress(ProjectPhase $phase): void
    {
        $total     = $phase->tasks()->count();
        $completed = $phase->tasks()->where('status', 'completed')->count();

        $progress = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

        $status = match (true) {
            $progress === 100                => 'completed',
            $progress > 0 && $phase->status === 'pending' => 'in_progress',
            default                          => $phase->status,
        };

        $phase->update([
            'progress_percent' => $progress,
            'status'           => $status,
        ]);
    }

    /**
     * Mark a task complete, then update its phase's progress.
     */
    public function completeTask(ProjectTask $task): void
    {
        $task->update([
            'status'       => 'completed',
            'completed_at' => now()->toDateString(),
        ]);

        $this->updatePhaseProgress($task->phase);
    }

    /**
     * Record material usage and update phase + project spend.
     */
    public function recordMaterialUsage(ConstructionProject $project, array $data): MaterialUsage
    {
        $usage = MaterialUsage::create(array_merge($data, [
            'construction_project_id' => $project->id,
            'company_id'              => $project->company_id,
        ]));

        if ($usage->project_phase_id) {
            $this->recalculatePhaseSpend($usage->phase);
        }

        $this->recalculateProjectSpend($project);

        return $usage;
    }
}
