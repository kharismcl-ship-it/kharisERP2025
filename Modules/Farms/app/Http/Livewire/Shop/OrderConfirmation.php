<?php

namespace Modules\Farms\Http\Livewire\Shop;

use Livewire\Component;
use Modules\Farms\Models\FarmOrder;

class OrderConfirmation extends Component
{
    public FarmOrder $order;

    public function mount(FarmOrder $order): void
    {
        abort_unless($order->payment_status === 'paid', 404);
        $this->order = $order->load('items.produceInventory');
    }

    public function render()
    {
        return view('farms::livewire.shop.order-confirmation')
            ->layout('farms::layouts.public', ['title' => 'Order Confirmed — Alpha Farms']);
    }
}
