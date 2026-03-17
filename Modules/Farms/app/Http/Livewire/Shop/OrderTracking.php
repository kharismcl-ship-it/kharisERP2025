<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Modules\Farms\Models\FarmOrder;

class OrderTracking extends Component
{
    public string $ref    = '';
    public string $phone  = '';
    public ?FarmOrder $order = null;
    public ?string $error = null;

    public function track(): void
    {
        $this->error = null;
        $this->order = null;

        $this->validate([
            'ref'   => ['required', 'string'],
            'phone' => ['required', 'string'],
        ]);

        $order = FarmOrder::where('ref', strtoupper(trim($this->ref)))
            ->where('customer_phone', trim($this->phone))
            ->with('items')
            ->first();

        if (! $order) {
            $this->error = 'No order found with that reference and phone number.';
            return;
        }

        $this->order = $order;
    }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'pending'    => 'Order Received',
            'confirmed'  => 'Order Confirmed',
            'processing' => 'Being Prepared',
            'ready'      => 'Ready for Pickup / Dispatch',
            'delivered'  => 'Delivered',
            'cancelled'  => 'Cancelled',
            default      => ucfirst($status),
        };
    }

    public function statusColor(string $status): string
    {
        return match ($status) {
            'pending'    => 'yellow',
            'confirmed'  => 'blue',
            'processing' => 'indigo',
            'ready'      => 'teal',
            'delivered'  => 'green',
            'cancelled'  => 'red',
            default      => 'gray',
        };
    }

    public function render()
    {
        return view('farms::livewire.shop.order-tracking')
            ->layout('farms::layouts.public', ['title' => 'Track Order — Alpha Farms']);
    }
}
