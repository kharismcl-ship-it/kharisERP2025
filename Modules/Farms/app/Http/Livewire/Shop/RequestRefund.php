<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Modules\Farms\Models\FarmOrder;
use Modules\Farms\Models\FarmReturnRequest;

class RequestRefund extends Component
{
    public FarmOrder $order;

    public string $reason      = '';
    public string $description = '';

    public bool $submitted = false;

    public function mount(FarmOrder $order): void
    {
        if (! auth('shop_customer')->check()) {
            session(['farm_shop_intended' => url()->current()]);
            $this->redirect(route('farm-shop.login'));
            return;
        }

        abort_unless(
            $order->shop_customer_id === auth('shop_customer')->id(),
            403
        );

        // Only allow refund requests for confirmed or delivered orders that are paid
        abort_unless(
            in_array($order->status, ['confirmed', 'processing', 'ready', 'delivered']) &&
            $order->payment_status === 'paid',
            403
        );

        // Block if a request already exists
        if (FarmReturnRequest::where('farm_order_id', $order->id)->exists()) {
            $this->submitted = true;
        }

        $this->order = $order->load('items');
    }

    public function submit(): void
    {
        $this->validate([
            'reason'      => ['required', 'string', 'in:' . implode(',', array_keys(FarmReturnRequest::REASONS))],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        FarmReturnRequest::create([
            'company_id'    => $this->order->company_id,
            'farm_order_id' => $this->order->id,
            'reason'        => $this->reason,
            'description'   => $this->description ?: null,
            'status'        => 'pending',
        ]);

        $this->submitted = true;
    }

    public function render()
    {
        return view('farms::livewire.shop.request-refund')
            ->layout('farms::layouts.public', ['title' => 'Request Refund — Order ' . $this->order->ref]);
    }
}
