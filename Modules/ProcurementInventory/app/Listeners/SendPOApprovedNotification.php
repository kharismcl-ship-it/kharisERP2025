<?php

namespace Modules\ProcurementInventory\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\ProcurementInventory\Events\PurchaseOrderApproved;

class SendPOApprovedNotification
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(PurchaseOrderApproved $event): void
    {
        $po     = $event->purchaseOrder;
        $vendor = $po->vendor;

        if (! $vendor) {
            return;
        }

        $vendorEmail = $vendor->contact_email ?? $vendor->email;
        if (! $vendorEmail) {
            return;
        }

        $data = [
            'vendor_name'   => $vendor->name,
            'po_number'     => $po->po_number,
            'order_date'    => $po->order_date?->format('d M Y') ?? now()->format('d M Y'),
            'expected_date' => $po->expected_delivery_date?->format('d M Y') ?? 'To be confirmed',
            'total_amount'  => number_format((float) $po->total, 2),
            'currency'      => 'GHS',
            'delivery_terms'=> $po->delivery_terms ?? 'Standard delivery',
            'notes'         => $po->notes ?? '',
        ];

        try {
            $this->comms->sendToModel(
                $vendor,
                'email',
                'procurement_po_approved',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendPOApprovedNotification failed', [
                'purchase_order_id' => $po->id,
                'vendor_id'         => $vendor->id,
                'error'             => $e->getMessage(),
            ]);
        }
    }
}