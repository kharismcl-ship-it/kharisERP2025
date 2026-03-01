<?php

namespace Modules\ProcurementInventory\Services;

use Illuminate\Support\Facades\DB;
use Modules\ProcurementInventory\Models\WarehouseTransfer;
use Modules\ProcurementInventory\Models\WarehouseTransferLine;

class WarehouseTransferService
{
    public function __construct(private readonly StockService $stockService) {}

    /**
     * Create a new warehouse transfer in draft status.
     *
     * @param  array{company_id: int, from_warehouse_id: int, to_warehouse_id: int, notes?: string, lines: array<array{item_id: int, quantity_requested: float, notes?: string}>}  $data
     */
    public function createTransfer(array $data): WarehouseTransfer
    {
        return DB::transaction(function () use ($data) {
            $transfer = WarehouseTransfer::create([
                'company_id'        => $data['company_id'],
                'from_warehouse_id' => $data['from_warehouse_id'],
                'to_warehouse_id'   => $data['to_warehouse_id'],
                'status'            => 'draft',
                'requested_by'      => auth()->id(),
                'notes'             => $data['notes'] ?? null,
            ]);

            foreach ($data['lines'] ?? [] as $line) {
                WarehouseTransferLine::create([
                    'warehouse_transfer_id' => $transfer->id,
                    'item_id'               => $line['item_id'],
                    'quantity_requested'    => $line['quantity_requested'],
                    'notes'                 => $line['notes'] ?? null,
                ]);
            }

            return $transfer;
        });
    }

    /**
     * Mark a draft transfer as in_transit (goods have left source warehouse).
     */
    public function dispatchTransfer(WarehouseTransfer $transfer): WarehouseTransfer
    {
        if (! $transfer->isDraft()) {
            throw new \Exception("Only draft transfers can be dispatched.");
        }

        $transfer->update([
            'status'         => 'in_transit',
            'transferred_at' => now(),
        ]);

        return $transfer->fresh();
    }

    /**
     * Complete the transfer: move stock from source → destination warehouse.
     * Each line's quantity_transferred can differ from quantity_requested (partial transfer).
     *
     * @param  array<int, float>  $quantities  Keyed by WarehouseTransferLine ID
     * @throws \Exception on insufficient stock
     */
    public function completeTransfer(WarehouseTransfer $transfer, array $quantities = []): WarehouseTransfer
    {
        if ($transfer->isCompleted() || $transfer->isCancelled()) {
            throw new \Exception("Transfer {$transfer->reference} cannot be completed.");
        }

        return DB::transaction(function () use ($transfer, $quantities) {
            foreach ($transfer->lines as $line) {
                $qty = isset($quantities[$line->id])
                    ? (float) $quantities[$line->id]
                    : (float) $line->quantity_requested;

                if ($qty <= 0) {
                    continue;
                }

                $this->stockService->transfer(
                    $transfer->company_id,
                    $transfer->from_warehouse_id,
                    $transfer->to_warehouse_id,
                    $line->item_id,
                    $qty,
                    $transfer->reference,
                    auth()->id()
                );

                $line->update(['quantity_transferred' => $qty]);
            }

            $transfer->update([
                'status'       => 'completed',
                'approved_by'  => auth()->id(),
                'completed_at' => now(),
            ]);

            return $transfer->fresh();
        });
    }

    /**
     * Cancel a draft or in_transit transfer (no stock movement).
     */
    public function cancelTransfer(WarehouseTransfer $transfer): WarehouseTransfer
    {
        if ($transfer->isCompleted()) {
            throw new \Exception("Completed transfers cannot be cancelled.");
        }

        $transfer->update(['status' => 'cancelled']);

        return $transfer->fresh();
    }
}