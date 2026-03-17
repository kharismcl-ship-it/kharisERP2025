<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Modules\Farms\Models\FarmLoyaltyPoint;
use Modules\Farms\Models\FarmOrder;
use Modules\Farms\Models\FarmReferral;
use Modules\Farms\Services\ShopSettingsService;
use Modules\PaymentsChannel\Facades\Payment;

class OrderPaymentReturn extends Component
{
    public FarmOrder $order;
    public string $paymentStatus = 'pending'; // pending | success | failed
    public string $message = '';

    public function mount(FarmOrder $order): void
    {
        $this->order = $order->load('company');

        try {
            $intent = $order->payIntents()->latest()->first();

            if (! $intent) {
                $this->paymentStatus = 'failed';
                $this->message = 'No payment record found.';
                return;
            }

            // Verify payment with gateway
            $result = Payment::verify($intent);

            if ($result && in_array($result->status ?? '', ['success', 'paid', 'completed'])) {
                $order->update([
                    'payment_status' => 'paid',
                    'status'         => 'confirmed',
                ]);
                $intent->update(['status' => 'completed']);

                // Award loyalty points (only if not already awarded)
                if ($order->shop_customer_id && $order->loyalty_points_earned === 0) {
                    try {
                        $settings = app(ShopSettingsService::class)->get($order->company_id);
                        if ($settings?->loyalty_enabled) {
                            $pointsEarned = (int) floor($order->total * (float) $settings->loyalty_points_per_ghs);
                            if ($pointsEarned > 0) {
                                FarmLoyaltyPoint::award(
                                    $order->shop_customer_id,
                                    $order->company_id,
                                    $pointsEarned,
                                    "Earned from order {$order->ref}",
                                    $order->id
                                );
                                $order->update(['loyalty_points_earned' => $pointsEarned]);
                            }
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Loyalty points award failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
                    }
                }

                // Credit referrer on the referred customer's first paid order
                if ($order->shop_customer_id) {
                    try {
                        $isPaidOrdersCount = FarmOrder::where('shop_customer_id', $order->shop_customer_id)
                            ->where('payment_status', 'paid')
                            ->count();

                        if ($isPaidOrdersCount <= 1) { // this is their first paid order
                            $pendingReferral = FarmReferral::where('referred_id', $order->shop_customer_id)
                                ->pending()
                                ->first();

                            if ($pendingReferral) {
                                FarmLoyaltyPoint::award(
                                    $pendingReferral->referrer_id,
                                    $pendingReferral->company_id,
                                    FarmReferral::REFERRAL_POINTS,
                                    "Referral bonus: friend placed first order"
                                );
                                $pendingReferral->update(['credited_at' => now()]);
                            }
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Referral credit failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
                    }
                }

                $this->paymentStatus = 'success';
                $this->message       = 'Payment successful!';
            } else {
                $intent->update(['status' => 'failed']);
                $this->paymentStatus = 'failed';
                $this->message       = $result->message ?? 'Payment could not be verified.';
            }
        } catch (\Exception $e) {
            Log::error('Farm order payment return error', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
            $this->paymentStatus = 'failed';
            $this->message       = 'An error occurred while verifying your payment.';
        }
    }

    public function render()
    {
        if ($this->paymentStatus === 'success') {
            return redirect()->route('farm-shop.order.confirmation', $this->order);
        }

        return view('farms::livewire.shop.order-payment-return')
            ->layout('farms::layouts.public', ['title' => 'Payment Return — Alpha Farms']);
    }
}
