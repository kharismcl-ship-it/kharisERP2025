<?php

namespace Modules\Farms\Console\Commands;

use Illuminate\Console\Command;
use Modules\Farms\Models\FarmRestockNotification;
use Modules\CommunicationCentre\Services\CommunicationService;

class FarmsNotifyRestockCommand extends Command
{
    protected $signature   = 'farms:notify-restock';
    protected $description = 'Send restock notifications to subscribers whose products are back in stock';

    public function handle(CommunicationService $comm): void
    {
        // Find pending notifications where the product now has stock
        $notifications = FarmRestockNotification::pending()
            ->with(['product', 'shopCustomer'])
            ->whereHas('product', fn ($q) => $q->where('current_stock', '>', 0)->whereIn('status', ['in_stock', 'low_stock']))
            ->get();

        if ($notifications->isEmpty()) {
            $this->info('No restock notifications to send.');
            return;
        }

        $sent = 0;
        foreach ($notifications as $notification) {
            $product  = $notification->product;
            $name     = $notification->shopCustomer?->name ?? 'Customer';
            $email    = $notification->email ?? $notification->shopCustomer?->email;
            $phone    = $notification->phone ?? $notification->shopCustomer?->phone;
            $shopUrl  = url('/farm-shop/products/' . $product->id);

            $subject = "Good news! {$product->product_name} is back in stock";
            $body    = "Hi {$name},\n\n"
                . "Great news — {$product->product_name} is now back in stock at GHS " . number_format($product->unit_price, 2) . " per {$product->unit}.\n\n"
                . "Order now before stock runs out again: {$shopUrl}\n\n"
                . "Thank you for your patience!";

            try {
                if ($email) {
                    $comm->sendRawEmail($email, $name, $subject, nl2br($body));
                }
                if ($phone) {
                    $smsBody = "{$product->product_name} is back in stock! GHS {$product->unit_price}/{$product->unit}. Order: {$shopUrl}";
                    $comm->sendRaw('sms', $phone, null, $smsBody);
                }

                $notification->update(['notified_at' => now()]);
                $sent++;
            } catch (\Throwable $e) {
                $this->warn("Failed to notify #{$notification->id}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$sent} restock notification(s).");
    }
}
