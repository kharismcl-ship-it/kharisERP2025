<?php

namespace Modules\Requisition\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\Invoice;
use Modules\Requisition\Events\RequisitionStatusChanged;
use Modules\Requisition\Models\RequisitionActivity;

class CreateIntercompanyInvoiceOnFulfillment
{
    public function handle(RequisitionStatusChanged $event): void
    {
        $requisition = $event->requisition;

        // Only fire when status becomes 'fulfilled' and is cross-company
        if ($requisition->status !== 'fulfilled') {
            return;
        }

        if (! $requisition->target_company_id) {
            return;
        }

        if ($requisition->target_company_id === $requisition->company_id) {
            return;
        }

        try {
            // Get the requesting company name for the invoice customer_name
            $requestingCompany = \App\Models\Company::find($requisition->company_id);
            $customerName      = $requestingCompany?->name ?? "Company #{$requisition->company_id}";

            // Create invoice in the target company (the one being billed / fulfilling the request)
            $invoice = Invoice::create([
                'company_id'    => $requisition->target_company_id,
                'type'          => 'customer',
                'customer_name' => $customerName,
                'invoice_date'  => now()->toDateString(),
                'due_date'      => now()->addDays(30)->toDateString(),
                'status'        => 'draft',
                'sub_total'     => $requisition->total_estimated_cost ?? 0,
                'tax_total'     => 0,
                'total'         => $requisition->total_estimated_cost ?? 0,
                'module'        => 'requisition',
                'entity_type'   => 'requisition',
                'entity_id'     => $requisition->id,
                'notes'         => "Auto-generated from intercompany requisition {$requisition->reference}",
            ]);

            RequisitionActivity::log(
                $requisition,
                'intercompany_invoice_created',
                "Intercompany invoice #{$invoice->id} created for {$customerName}.",
            );
        } catch (\Throwable $e) {
            Log::error('CreateIntercompanyInvoiceOnFulfillment failed', [
                'requisition_id' => $requisition->id,
                'error'          => $e->getMessage(),
            ]);
        }
    }
}