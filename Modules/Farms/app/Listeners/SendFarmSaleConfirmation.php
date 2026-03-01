<?php

namespace Modules\Farms\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Farms\Events\FarmSaleCreated;

class SendFarmSaleConfirmation
{
    public function __construct(private readonly CommunicationService $comms) {}

    public function handle(FarmSaleCreated $event): void
    {
        $sale = $event->farmSale;

        // Only send if buyer contact is available
        if (empty($sale->buyer_name)) {
            return;
        }

        $data = [
            'buyer_name'   => $sale->buyer_name,
            'farm_name'    => $sale->farm?->name ?? 'Farm',
            'product_name' => $sale->product_name,
            'product_type' => ucfirst($sale->product_type),
            'quantity'     => number_format((float) $sale->quantity, 3),
            'unit'         => $sale->unit,
            'unit_price'   => number_format((float) $sale->unit_price, 2),
            'total_amount' => number_format((float) $sale->total_amount, 2),
            'currency'     => 'GHS',
            'sale_date'    => $sale->sale_date?->format('d M Y') ?? now()->format('d M Y'),
        ];

        try {
            $this->comms->sendToModel(
                $sale,
                'email',
                'farms_sale_confirmation',
                $data
            );
        } catch (\Throwable $e) {
            Log::warning('SendFarmSaleConfirmation failed', [
                'farm_sale_id' => $sale->id,
                'error'        => $e->getMessage(),
            ]);
        }
    }
}