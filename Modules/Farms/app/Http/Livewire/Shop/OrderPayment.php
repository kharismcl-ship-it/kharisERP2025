<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Modules\Farms\Models\FarmOrder;
use Modules\PaymentsChannel\Facades\Payment;
use Modules\PaymentsChannel\Models\PayIntent;

class OrderPayment extends Component
{
    public FarmOrder $order;

    public ?PayIntent $payIntent = null;

    public string $selectedPaymentMethod = '';

    public array $groupedPaymentMethods = [];

    public function mount(FarmOrder $order): void
    {
        abort_if($order->payment_status === 'paid', 302, route('farm-shop.order.confirmation', $order));

        $this->order = $order->load('items', 'company');

        $companyId = $this->order->company_id;

        $methods = Payment::getGroupedPaymentMethods($companyId, [
            'payment_mode' => 'online',
        ]);

        // MoMo-First: move mobile_money / momo groups to the front (Ghana priority)
        $momoKeys = ['mobile_money', 'momo', 'mobile money'];
        $momo = array_filter($methods, fn ($k) => in_array(strtolower($k), $momoKeys), ARRAY_FILTER_USE_KEY);
        $rest = array_filter($methods, fn ($k) => ! in_array(strtolower($k), $momoKeys), ARRAY_FILTER_USE_KEY);
        $this->groupedPaymentMethods = array_merge($momo, $rest);

        // Load or create payment intent
        $this->payIntent = $order->payIntents()->latest()->first();

        if (! $this->payIntent) {
            $this->createPaymentIntent();
        } elseif ($this->payIntent->payMethod) {
            $this->selectedPaymentMethod = $this->payIntent->payMethod->code;
        }
    }

    protected function createPaymentIntent(): void
    {
        $options = [
            'amount'     => (float) $this->order->total,
            'currency'   => 'GHS',
            'return_url' => route('farm-shop.order.payment-return', $this->order),
            'metadata'   => [
                'farm_order_id' => $this->order->id,
                'order_ref'     => $this->order->ref,
            ],
        ];

        if ($this->selectedPaymentMethod) {
            $options['method_code'] = $this->selectedPaymentMethod;
        }

        $this->payIntent = Payment::createIntentForModel(
            payable: $this->order,
            options: $options
        );
    }

    public function initiatePayment()
    {
        try {
            // Recreate intent if method changed or status not pending
            if ($this->payIntent && $this->payIntent->status !== 'pending') {
                $this->payIntent->delete();
                $this->createPaymentIntent();
            } elseif ($this->selectedPaymentMethod &&
                $this->payIntent?->payMethod?->code !== $this->selectedPaymentMethod) {
                $this->payIntent?->delete();
                $this->createPaymentIntent();
            }

            $init = Payment::initialize($this->payIntent);

            if ($init->redirect_url) {
                return redirect()->away($init->redirect_url);
            }

            // Manual payment — mark as paid directly
            $this->order->update(['payment_status' => 'paid', 'status' => 'confirmed']);

            return redirect()->route('farm-shop.order.confirmation', $this->order);

        } catch (\Exception $e) {
            Log::error('Farm order payment failed', [
                'order_id' => $this->order->id,
                'error'    => $e->getMessage(),
            ]);
            session()->flash('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    public function changePaymentMethod(): void
    {
        if ($this->payIntent && $this->payIntent->status === 'pending') {
            $this->payIntent->update(['status' => 'cancelled']);
            $this->createPaymentIntent();
        }
    }

    public function render()
    {
        return view('farms::livewire.shop.order-payment')
            ->layout('farms::layouts.public', ['title' => 'Pay for Order — Alpha Farms']);
    }
}
