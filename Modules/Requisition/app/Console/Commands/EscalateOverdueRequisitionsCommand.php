<?php

namespace Modules\Requisition\Console\Commands;

use Illuminate\Console\Command;
use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Models\RequisitionActivity;

class EscalateOverdueRequisitionsCommand extends Command
{
    protected $signature = 'requisition:escalate-overdue';

    protected $description = 'Auto-escalate urgency for overdue requisitions that have not been resolved.';

    public function handle(): int
    {
        $escalationMap = [
            'low'    => ['days' => 1, 'escalate_to' => 'medium'],
            'medium' => ['days' => 2, 'escalate_to' => 'high'],
            'high'   => ['days' => 3, 'escalate_to' => 'urgent'],
        ];

        $activeStatuses = ['draft', 'submitted', 'under_review', 'pending_revision'];
        $escalated      = 0;

        foreach ($escalationMap as $currentUrgency => $rule) {
            $overdue = Requisition::withoutGlobalScopes()
                ->whereIn('status', $activeStatuses)
                ->where('urgency', $currentUrgency)
                ->whereNotNull('due_by')
                ->where('due_by', '<', now()->subDays($rule['days'])->toDateString())
                ->get();

            foreach ($overdue as $requisition) {
                $requisition->withoutEvents(function () use ($requisition, $rule) {
                    $requisition->update(['urgency' => $rule['escalate_to']]);
                });

                RequisitionActivity::log(
                    $requisition,
                    'status_changed',
                    "Urgency auto-escalated from {$currentUrgency} to {$rule['escalate_to']} (overdue by {$rule['days']}+ days).",
                );

                $escalated++;
            }
        }

        $this->info("Escalated {$escalated} overdue requisition(s).");

        return self::SUCCESS;
    }
}