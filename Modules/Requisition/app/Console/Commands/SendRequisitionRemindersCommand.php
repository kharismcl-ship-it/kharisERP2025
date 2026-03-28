<?php

namespace Modules\Requisition\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Models\RequisitionActivity;
use Modules\Requisition\Models\RequisitionReminderRule;

class SendRequisitionRemindersCommand extends Command
{
    protected $signature = 'requisition:send-reminders';

    protected $description = 'Send reminder notifications for stalled requisitions based on configured reminder rules.';

    public function __construct(private readonly CommunicationService $comms)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $rules = RequisitionReminderRule::withoutGlobalScopes()->active()->get();

        if ($rules->isEmpty()) {
            $this->info('No active reminder rules.');
            return self::SUCCESS;
        }

        $urgencyLevels  = ['low' => 0, 'medium' => 1, 'high' => 2, 'urgent' => 3];
        $urgencyUp      = array_flip($urgencyLevels);
        $totalSent      = 0;

        foreach ($rules as $rule) {
            $cutoff = now()->subHours($rule->hours_after_trigger);

            // Find stalled requisitions: in trigger_status, updated_at before cutoff
            $requisitions = Requisition::withoutGlobalScopes()
                ->where('company_id', $rule->company_id)
                ->where('status', $rule->trigger_status)
                ->where('updated_at', '<=', $cutoff)
                ->whereDoesntHave('activities', function ($q) use ($cutoff) {
                    // Skip if a reminder was already sent recently (within the reminder window)
                    $q->where('action', 'reminder_sent')->where('created_at', '>=', $cutoff);
                })
                ->with(['requesterEmployee', 'approvers.employee'])
                ->get();

            foreach ($requisitions as $req) {
                $channels = $rule->reminder_channels ?? ['email'];

                // Notify requester
                if ($rule->notify_requester && $req->requesterEmployee) {
                    $emp = $req->requesterEmployee;
                    $this->sendReminder($channels, $emp, $req);
                }

                // Notify pending approvers
                if ($rule->notify_approvers) {
                    foreach ($req->approvers->where('decision', 'pending') as $approver) {
                        if ($approver->employee) {
                            $this->sendReminder($channels, $approver->employee, $req);
                        }
                    }
                }

                // Escalate urgency
                if ($rule->escalate_urgency) {
                    $currentLevel = $urgencyLevels[$req->urgency] ?? 0;
                    if ($currentLevel < 3) {
                        $newUrgency = $urgencyUp[$currentLevel + 1];
                        $req->withoutEvents(fn () => $req->update(['urgency' => $newUrgency]));
                        RequisitionActivity::log(
                            $req,
                            'status_changed',
                            "Urgency auto-escalated from {$req->urgency} to {$newUrgency} via reminder rule '{$rule->name}'.",
                        );
                    }
                }

                // Log reminder activity
                RequisitionActivity::log($req, 'reminder_sent', "Reminder sent via rule '{$rule->name}'.");
                $totalSent++;
            }
        }

        $this->info("Reminders processed for {$totalSent} requisition(s).");

        return self::SUCCESS;
    }

    private function sendReminder(array $channels, $employee, Requisition $req): void
    {
        $data = [
            'reference' => $req->reference,
            'title'     => $req->title,
            'status'    => $req->status,
            'urgency'   => $req->urgency,
        ];

        foreach ($channels as $channel) {
            try {
                if ($channel === 'email' && $employee->getCommEmail()) {
                    $this->comms->sendFromTemplate(
                        'email',
                        'requisition_reminder',
                        $employee->getCommEmail(),
                        $employee->getCommName(),
                        $data
                    );
                } elseif (in_array($channel, ['sms', 'whatsapp']) && method_exists($employee, 'getCommPhone') && $employee->getCommPhone()) {
                    $this->comms->sendFromTemplate(
                        $channel,
                        'requisition_reminder',
                        $employee->getCommPhone(),
                        $employee->getCommName(),
                        $data
                    );
                }
            } catch (\Throwable $e) {
                Log::warning("SendRequisitionReminders: failed to send via {$channel}", [
                    'requisition' => $req->reference,
                    'employee_id' => $employee->id,
                    'error'       => $e->getMessage(),
                ]);
            }
        }
    }
}