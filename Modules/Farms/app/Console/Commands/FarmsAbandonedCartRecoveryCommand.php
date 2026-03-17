<?php

namespace Modules\Farms\Console\Commands;

use Illuminate\Console\Command;
use Modules\Farms\Models\FarmAbandonedCart;
use Modules\CommunicationCentre\Services\CommunicationService;

class FarmsAbandonedCartRecoveryCommand extends Command
{
    protected $signature   = 'farms:abandoned-cart-recovery';
    protected $description = 'Send recovery emails/SMS to customers who abandoned their farm shop carts';

    public function handle(CommunicationService $comm): void
    {
        // Carts abandoned more than 30 min ago, not yet notified, with contact info
        $carts = FarmAbandonedCart::whereNull('notified_at')
            ->where('updated_at', '<=', now()->subMinutes(30))
            ->where(fn ($q) => $q->whereNotNull('customer_email')->orWhereNotNull('customer_phone'))
            ->with('shopCustomer')
            ->get();

        if ($carts->isEmpty()) {
            $this->info('No abandoned carts to process.');
            return;
        }

        $sent = 0;
        foreach ($carts as $cart) {
            $name      = $cart->customer_name ?? 'there';
            $cartTotal = 'GHS ' . number_format($cart->cart_total, 2);
            $items     = collect($cart->cart_data);
            $itemList  = $items->map(fn ($i) => "• {$i['product_name']} × {$i['quantity']} {$i['unit']}")->implode("\n");
            $cartUrl   = url('/farm-shop/cart');

            $subject = "You left something in your cart — {$cartTotal}";
            $emailBody = "<p>Hi {$name},</p>"
                . "<p>You left the following items in your farm shop cart:</p>"
                . "<pre>{$itemList}</pre>"
                . "<p><strong>Cart Total: {$cartTotal}</strong></p>"
                . "<p><a href='{$cartUrl}'>Complete your order now →</a></p>"
                . "<p>Your items are reserved for a limited time. Order fresh farm produce delivered to your door!</p>";

            $smsBody = "Hi {$name}! You left items in your cart ({$cartTotal}). Complete your order: {$cartUrl}";

            try {
                if ($cart->customer_email) {
                    $comm->sendRawEmail($cart->customer_email, $name, $subject, $emailBody);
                }
                if ($cart->customer_phone) {
                    $comm->sendRaw('sms', $cart->customer_phone, null, $smsBody);
                }

                $cart->update(['notified_at' => now()]);
                $sent++;
            } catch (\Throwable $e) {
                $this->warn("Failed to notify cart #{$cart->id}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$sent} abandoned cart recovery message(s).");
    }
}
