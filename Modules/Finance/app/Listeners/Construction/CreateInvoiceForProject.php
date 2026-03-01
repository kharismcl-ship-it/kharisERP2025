<?php

namespace Modules\Finance\Listeners\Construction;

use Illuminate\Support\Facades\Log;
use Modules\Construction\Events\ProjectMilestoneCompleted;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\InvoiceLine;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;

class CreateInvoiceForProject
{
    /**
     * When a construction project phase is completed, create a milestone invoice
     * and post the revenue double-entry to the Finance GL:
     *
     *   DR  1110 Accounts Receivable   (phase contract value)
     *   CR  4100 Revenue               (phase contract value)
     */
    public function handle(ProjectMilestoneCompleted $event): void
    {
        $project   = $event->project;
        $phase     = $event->phase;
        $companyId = $project->company_id;

        // Use phase budget as milestone invoice amount; fall back to contract_value / phase count
        $phaseValue = (float) ($phase->budget ?? 0);
        if ($phaseValue <= 0) {
            $phaseCount = $project->phases()->count() ?: 1;
            $phaseValue = round((float) $project->contract_value / $phaseCount, 2);
        }

        if ($phaseValue <= 0) {
            Log::info('CreateInvoiceForProject: skipped — no contract value', ['project_id' => $project->id]);
            return;
        }

        $invoiceNumber = 'CINV-' . strtoupper(substr($project->slug ?? $project->id, 0, 6)) . '-' . strtoupper(uniqid());

        $invoice = Invoice::create([
            'company_id'              => $companyId,
            'type'                    => 'customer',
            'customer_name'           => $project->client_name ?? 'Client',
            'customer_type'           => 'construction_client',
            'customer_id'             => $project->id,
            'invoice_number'          => $invoiceNumber,
            'invoice_date'            => now(),
            'due_date'                => now()->addDays(30),
            'status'                  => 'sent',
            'sub_total'               => $phaseValue,
            'tax_total'               => 0,
            'total'                   => $phaseValue,
            'construction_project_id' => $project->id,
            'module'                  => 'construction',
            'entity_type'             => 'project_phase',
            'entity_id'               => $phase->id,
        ]);

        InvoiceLine::create([
            'invoice_id'  => $invoice->id,
            'description' => "Phase: {$phase->name} — {$project->name}",
            'quantity'    => 1,
            'unit_price'  => $phaseValue,
            'line_total'  => $phaseValue,
        ]);

        // Link invoice back to project
        $project->update(['invoice_id' => $invoice->id]);

        // Post GL double-entry
        $entry = JournalEntry::create([
            'company_id'  => $companyId,
            'date'        => now(),
            'reference'   => $invoiceNumber,
            'description' => "Construction milestone invoice — {$phase->name} ({$project->name})",
        ]);

        if ($arAccount = $this->account('1110', $companyId)) {
            JournalLine::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $arAccount->id,
                'debit'            => $phaseValue,
                'credit'           => 0,
            ]);
        }

        if ($revenueAccount = $this->account('4100', $companyId)) {
            JournalLine::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $revenueAccount->id,
                'debit'            => 0,
                'credit'           => $phaseValue,
            ]);
        }

        Log::info('CreateInvoiceForProject: milestone invoice posted', [
            'invoice_id' => $invoice->id,
            'project_id' => $project->id,
            'phase_id'   => $phase->id,
            'amount'     => $phaseValue,
        ]);
    }

    private function account(string $code, ?int $companyId): ?Account
    {
        return Account::where('code', $code)
            ->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)->orWhereNull('company_id');
            })
            ->first();
    }
}
