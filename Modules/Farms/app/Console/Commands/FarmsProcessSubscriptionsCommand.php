<?php

namespace Modules\Farms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Farms\Events\FarmOrderPlaced;
use Modules\Farms\Models\FarmOrder;
use Modules\Farms\Models\FarmOrderItem;
use Modules\Farms\Models\FarmProduceInventory;
use Modules\Farms\Models\FarmSubscription;

class FarmsProcessSubscriptionsCommand extends Command
{
    protected $signature = 'farms:process-subscriptions';

    protected $description = 'Auto-generate farm orders for due subscriptions';

    public function handle(): int
    {
        $due = FarmSubscription::with(['shopCustomer', 'company'])
            ->active()
            ->due()
            ->get();

        $this->info("Processing {$due->count()} due subscriptions...");

        foreach ($due as $sub) {
            try {
                $this->processSubscription($sub);
            } catch (\Throwable $e) {
                Log::error('Subscription processing failed', [
                    'subscription_id' => $sub->id,
                    'error'           => $e->getMessage(),
                ]);
                $this->warn("Failed subscription #{$sub->id}: {$e->getMessage()}");
            }
        }

        $this->info('Done.');
        return self::SUCCESS;
    }

    private function processSubscription(FarmSubscription $sub): void
    {
        $items = $sub->items ?? [];
        if (empty($items)) {
            $sub->update(['status' => 'cancelled', 'cancelled_at' => now()]);
            return;
        }

        // Validate stock
        foreach ($items as $item) {
            $inventory = FarmProduceInventory::find($item['inventory_id']);
            if (! $inventory || $inventory->current_stock < $item['quantity']) {
                // Skip this cycle — notify customer
                $this->notifyInsufficientStock($sub, $item['product_name'] ?? 'item');
                $sub->advanceNextOrderDate(); // still advance so we try again next cycle
                return;
            }
        }

        $subtotal    = (float) $sub->subtotal;
        $deliveryFee = $sub->delivery_type === 'pickup' ? 0 : 0; // managed by shop settings — simplest: 0 for now

        $order = FarmOrder::create([
            'company_id'       => $sub->company_id,
            'shop_customer_id' => $sub->shop_customer_id,
            'customer_name'    => $sub->shopCustomer->name,
            'customer_phone'   => $sub->shopCustomer->phone ?? '',
            'customer_email'   => $sub->shopCustomer->email,
            'delivery_type'    => $sub->delivery_type,
            'delivery_address' => $sub->delivery_address,
            'delivery_landmark'=> $sub->delivery_landmark,
            'subtotal'         => $subtotal,
            'delivery_fee'     => $deliveryFee,
            'discount_amount'  => 0,
            'total'            => $subtotal + $deliveryFee,
            'notes'            => ($sub->notes ? $sub->notes . ' — ' : '') . 'Auto-generated subscription order.',
            'status'           => 'confirmed',
            'payment_status'   => 'pending',
        ]);

        foreach ($items as $item) {
            FarmOrderItem::create([
                'farm_order_id'             => $order->id,
                'farm_produce_inventory_id' => $item['inventory_id'],
                'product_name'              => $item['product_name'],
                'unit'                      => $item['unit'],
                'quantity'                  => $item['quantity'],
                'unit_price'                => $item['unit_price'],
                'subtotal'                  => round($item['quantity'] * $item['unit_price'], 2),
            ]);

            FarmProduceInventory::where('id', $item['inventory_id'])
                ->decrement('current_stock', $item['quantity']);

            $inv = FarmProduceInventory::find($item['inventory_id']);
            if ($inv && $inv->current_stock <= 0) {
                $inv->update(['status' => 'depleted']);
            } elseif ($inv && $inv->current_stock <= 10) {
                $inv->update(['status' => 'low_stock']);
            }
        }

        $sub->advanceNextOrderDate();

        FarmOrderPlaced::dispatch($order);

        $this->line("Created order {$order->ref} for subscription #{$sub->id}");
    }

    private function notifyInsufficientStock(FarmSubscription $sub, string $productName): void
    {
        try {
            $comm = app(CommunicationService::class);
            $shopName = $sub->company->name ?? 'Farm Shop';
            $msg = "Hi {$sub->shopCustomer->name}, your {$sub->frequency} subscription order from {$shopName} could not be processed this cycle because '{$productName}' is out of stock. We'll try again on your next delivery date.";

            if ($sub->shopCustomer->email) {
                $comm->sendRawEmail($sub->shopCustomer->email, $sub->shopCustomer->name, 'Subscription Update — Stock Unavailable', "<p>{$msg}</p>");
            }
            if ($sub->shopCustomer->phone) {
                $comm->sendRaw('sms', $sub->shopCustomer->phone, null, $msg);
            }
        } catch (\Throwable) {
            // notification failure is non-fatal
        }
    }
}
