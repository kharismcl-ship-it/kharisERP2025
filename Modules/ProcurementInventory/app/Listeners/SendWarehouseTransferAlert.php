<?php

namespace Modules\ProcurementInventory\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\ProcurementInventory\Events\WarehouseTransferCompleted;

class SendWarehouseTransferAlert
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(WarehouseTransferCompleted $event): void
    {
        $transfer = $event->transfer;

        $totalItems = $transfer->lines->sum('quantity_transferred');

        $data = [
            'reference'      => $transfer->reference,
            'from_warehouse' => $transfer->fromWarehouse?->name ?? 'Unknown',
            'to_warehouse'   => $transfer->toWarehouse?->name ?? 'Unknown',
            'completed_at'   => $transfer->completed_at?->format('d M Y H:i') ?? now()->format('d M Y H:i'),
            'total_lines'    => $transfer->lines->count(),
            'total_qty'      => number_format((float) $totalItems, 2),
            'approved_by'    => $transfer->approvedBy?->name ?? 'System',
        ];

        try {
            $this->comms->sendToModel(
                $transfer,
                'email',
                'procurement_warehouse_transfer_completed',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendWarehouseTransferAlert failed', [
                'warehouse_transfer_id' => $transfer->id,
                'reference'             => $transfer->reference,
                'error'                 => $e->getMessage(),
            ]);
        }
    }
}