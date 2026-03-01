<?php

namespace Modules\Finance\Listeners\ProcurementInventory;

use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\Account;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\JournalLine;
use Modules\ProcurementInventory\Events\GoodsReceived;

class PostInventoryOnGoodsReceived
{
    /**
     * When goods are confirmed received:
     *   DR  1300 Inventory         (value of received goods)
     *   CR  2110 Accounts Payable  (liability to vendor)
     *
     * Note: only posts the incremental value of this GRN, not the full PO amount.
     */
    public function handle(GoodsReceived $event): void
    {
        $grn       = $event->goodsReceipt->load('lines', 'purchaseOrder');
        $companyId = $grn->company_id;

        // Total value = sum of (qty_received × unit_price) across lines
        $receivedValue = $grn->lines->sum(
            fn ($line) => (float) $line->quantity_received * (float) $line->unit_price
        );

        if ($receivedValue <= 0) {
            return;
        }

        $poNumber = $grn->purchaseOrder?->po_number ?? "GRN-{$grn->id}";

        $entry = JournalEntry::create([
            'company_id'  => $companyId,
            'date'        => $grn->receipt_date ?? now(),
            'reference'   => "GRN-{$grn->grn_number}",
            'description' => "Inventory receipt — GRN {$grn->grn_number} against PO {$poNumber}",
        ]);

        $inventoryAccount = $this->account('1300', $companyId);
        $apAccount        = $this->account('2110', $companyId);

        if ($inventoryAccount) {
            JournalLine::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $inventoryAccount->id,
                'debit'            => $receivedValue,
                'credit'           => 0,
            ]);
        }

        if ($apAccount) {
            JournalLine::create([
                'journal_entry_id' => $entry->id,
                'account_id'       => $apAccount->id,
                'debit'            => 0,
                'credit'           => $receivedValue,
            ]);
        }

        Log::info('PostInventoryOnGoodsReceived: inventory journal posted', [
            'journal_entry_id' => $entry->id,
            'grn_id'           => $grn->id,
            'grn_number'       => $grn->grn_number,
            'received_value'   => $receivedValue,
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
