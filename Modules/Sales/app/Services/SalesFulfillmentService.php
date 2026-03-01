<?php

namespace Modules\Sales\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Sales\Contracts\FulfillmentHandlerInterface;
use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Services\Fulfillment\ConstructionFulfillmentHandler;
use Modules\Sales\Services\Fulfillment\FarmFulfillmentHandler;
use Modules\Sales\Services\Fulfillment\FleetFulfillmentHandler;
use Modules\Sales\Services\Fulfillment\HostelsFulfillmentHandler;
use Modules\Sales\Services\Fulfillment\InventoryFulfillmentHandler;
use Modules\Sales\Services\Fulfillment\PaperFulfillmentHandler;
use Modules\Sales\Services\Fulfillment\WaterFulfillmentHandler;

class SalesFulfillmentService
{
    /** @var FulfillmentHandlerInterface[] */
    protected array $handlers = [];

    public function __construct()
    {
        $this->handlers = [
            new WaterFulfillmentHandler(),
            new PaperFulfillmentHandler(),
            new FarmFulfillmentHandler(),
            new InventoryFulfillmentHandler(),
            new FleetFulfillmentHandler(),
            new ConstructionFulfillmentHandler(),
            new HostelsFulfillmentHandler(),
        ];
    }

    public function fulfill(SalesOrder $order): void
    {
        DB::transaction(function () use ($order) {
            $order->load('lines.catalogItem');

            foreach ($order->lines as $line) {
                $sourceModule = $line->catalogItem->source_module ?? null;

                if (! $sourceModule) {
                    $line->update(['fulfilled_quantity' => $line->quantity]);
                    continue;
                }

                $handled = false;

                foreach ($this->handlers as $handler) {
                    if ($handler->sourceModule() === $sourceModule) {
                        try {
                            $handler->handle($order, $line);
                            $handled = true;
                        } catch (\Throwable $e) {
                            Log::error("SalesFulfillmentService: handler {$sourceModule} failed for line {$line->id}: {$e->getMessage()}");
                        }
                        break;
                    }
                }

                if (! $handled) {
                    // No specific handler — mark fulfilled directly
                    $line->update(['fulfilled_quantity' => $line->quantity]);
                }
            }

            // Check if all lines are now fulfilled
            $order->refresh();
            $allFulfilled = $order->lines->every(fn ($l) => $l->fulfillment_status === 'fulfilled');

            if ($allFulfilled) {
                $order->update(['status' => 'fulfilled']);
            }
        });
    }
}