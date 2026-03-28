<?php

namespace Modules\Requisition\Console\Commands;

use Illuminate\Console\Command;
use Modules\Requisition\Events\RequisitionStatusChanged;
use Modules\Requisition\Models\RequisitionItem;
use Modules\Requisition\Models\RequisitionSchedule;

class ProcessRequisitionSchedulesCommand extends Command
{
    protected $signature = 'requisition:process-schedules';

    protected $description = 'Process all due recurring requisition schedules and create new draft requisitions.';

    public function handle(): int
    {
        $schedules = RequisitionSchedule::due()->with(['template', 'template.costCentre'])->get();

        if ($schedules->isEmpty()) {
            $this->info('No schedules due.');
            return self::SUCCESS;
        }

        $processed = 0;

        foreach ($schedules as $schedule) {
            $template = $schedule->template;

            if (! $template) {
                $this->warn("Schedule #{$schedule->id} ({$schedule->name}): template not found, skipping.");
                continue;
            }

            try {
                // Create requisition from template
                $requisition = \Modules\Requisition\Models\Requisition::create([
                    'company_id'             => $schedule->company_id,
                    'requester_employee_id'  => $schedule->requester_employee_id,
                    'cost_centre_id'         => $schedule->cost_centre_id ?? $template->cost_centre_id,
                    'template_id'            => $template->id,
                    'request_type'           => $template->request_type,
                    'urgency'                => $template->urgency,
                    'title'                  => $template->default_title ?? $template->name,
                    'description'            => $template->description,
                    'status'                 => 'draft',
                    'notification_channels'  => ['email', 'database'],
                ]);

                // Copy template default_items as RequisitionItem records
                $defaultItems = $template->default_items ?? [];
                foreach ($defaultItems as $item) {
                    RequisitionItem::create([
                        'requisition_id' => $requisition->id,
                        'description'    => $item['description'] ?? 'Item',
                        'quantity'       => $item['quantity'] ?? 1,
                        'unit'           => $item['unit'] ?? 'pcs',
                        'unit_cost'      => $item['unit_cost'] ?? null,
                        'notes'          => $item['notes'] ?? null,
                    ]);
                }

                // Auto-submit if configured
                if ($schedule->auto_submit) {
                    $requisition->update(['status' => 'submitted']);
                }

                // Update schedule timing
                $schedule->update([
                    'last_run_at' => now()->toDateString(),
                    'next_run_at' => $schedule->calculateNextRun()->toDateString(),
                ]);

                $this->info("Processed schedule '{$schedule->name}' → created {$requisition->reference}" . ($schedule->auto_submit ? ' (auto-submitted)' : ''));
                $processed++;
            } catch (\Throwable $e) {
                $this->error("Schedule #{$schedule->id} ({$schedule->name}) failed: {$e->getMessage()}");
            }
        }

        $this->info("Done. {$processed} schedule(s) processed.");

        return self::SUCCESS;
    }
}