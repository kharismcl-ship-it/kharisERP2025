<?php

namespace Modules\Finance\Services\Automation;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Models\AutomationSetting;
use Modules\Finance\Models\Invoice;

/**
 * Activates scheduled (draft) invoices whose invoice_date has arrived,
 * and flags overdue invoices that have passed their due_date.
 */
class InvoiceGenerationHandler
{
    public function execute(AutomationSetting $setting): array
    {
        $companyId = $setting->company_id;
        $processed = 0;
        $errors    = [];

        try {
            DB::beginTransaction();

            // 1. Activate draft invoices scheduled for today or earlier
            $activated = Invoice::where('company_id', $companyId)
                ->where('status', 'draft')
                ->whereDate('invoice_date', '<=', now()->toDateString())
                ->get();

            foreach ($activated as $invoice) {
                try {
                    $invoice->update(['status' => 'pending']);
                    $processed++;
                } catch (\Exception $e) {
                    $errors[] = "Invoice {$invoice->invoice_number}: {$e->getMessage()}";
                }
            }

            // 2. Mark pending invoices as overdue when past due_date
            $overdue = Invoice::where('company_id', $companyId)
                ->where('status', 'pending')
                ->whereDate('due_date', '<', now()->toDateString())
                ->get();

            foreach ($overdue as $invoice) {
                try {
                    $invoice->update(['status' => 'overdue']);
                    $processed++;
                } catch (\Exception $e) {
                    $errors[] = "Invoice {$invoice->invoice_number} overdue transition: {$e->getMessage()}";
                }
            }

            DB::commit();

            return [
                'success'           => empty($errors),
                'records_processed' => $processed,
                'details'           => [
                    'activated' => $activated->count(),
                    'marked_overdue' => $overdue->count(),
                    'errors'    => $errors,
                ],
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("InvoiceGenerationHandler failed for company {$companyId}", ['error' => $e->getMessage()]);

            return [
                'success'           => false,
                'error'             => $e->getMessage(),
                'records_processed' => $processed,
                'details'           => ['errors' => $errors],
            ];
        }
    }
}
