<?php

namespace Modules\Finance\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\InvoiceLine;
use Modules\Finance\Services\ThreeWayMatchService;

class SyncProcurementToExpensesCommand extends Command
{
    protected $signature = 'finance:sync-procurement {--limit=100 : Number of purchase orders to process}';

    protected $description = 'Sync approved procurement purchase orders to Finance vendor invoices and run three-way match';

    public function handle(ThreeWayMatchService $matchService): int
    {
        if (! class_exists(\Modules\ProcurementInventory\Models\PurchaseOrder::class)) {
            $this->warn('ProcurementInventory module not available.');
            return self::SUCCESS;
        }

        $limit = (int) $this->option('limit');

        // Fetch POs that are approved or beyond, not yet synced to Finance
        $pos = \Modules\ProcurementInventory\Models\PurchaseOrder::with('lines.item')
            ->whereIn('status', ['approved', 'ordered', 'partially_received', 'received', 'closed'])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('invoices')
                    ->whereColumn('invoices.purchase_order_id', 'purchase_orders.id')
                    ->where('invoices.type', 'vendor');
            })
            ->take($limit)
            ->get();

        if ($pos->isEmpty()) {
            $this->info('No new purchase orders to sync.');
            return self::SUCCESS;
        }

        $synced = 0;
        foreach ($pos as $po) {
            try {
                DB::transaction(function () use ($po, $matchService) {
                    $invoice = Invoice::create([
                        'company_id'         => $po->company_id,
                        'type'               => 'vendor',
                        'vendor_id'          => $po->vendor_id,
                        'purchase_order_id'  => $po->id,
                        'invoice_number'     => 'PINV-' . $po->po_number,
                        'invoice_date'       => $po->po_date ?? now()->toDateString(),
                        'due_date'           => now()->addDays(30)->toDateString(),
                        'status'             => 'pending',
                        'match_status'       => 'pending',
                        'sub_total'          => $po->subtotal,
                        'tax_total'          => $po->tax_total,
                        'total'              => $po->total,
                        'module'             => 'procurement',
                        'entity_type'        => \Modules\ProcurementInventory\Models\PurchaseOrder::class,
                        'entity_id'          => $po->id,
                    ]);

                    foreach ($po->lines as $line) {
                        InvoiceLine::create([
                            'invoice_id'  => $invoice->id,
                            'description' => $line->description,
                            'quantity'    => $line->quantity,
                            'unit_price'  => $line->unit_price,
                            'tax_rate'    => $line->tax_rate,
                            'tax_amount'  => $line->tax_amount,
                            'line_total'  => $line->line_total,
                        ]);
                    }

                    // Attempt three-way match immediately if GRN exists
                    $matchService->match($invoice);
                });
                $synced++;
                $this->line("  Synced PO #{$po->po_number} → invoice created.");
            } catch (\Throwable $e) {
                Log::error("SyncProcurementToExpenses failed for PO #{$po->id}: " . $e->getMessage());
                $this->warn("  Failed for PO #{$po->po_number}: " . $e->getMessage());
            }
        }

        $this->info("Synced {$synced} purchase order(s) to Finance.");

        return self::SUCCESS;
    }
}
