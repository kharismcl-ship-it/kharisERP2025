<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Shop;

use Livewire\Component;
use Modules\Sales\Models\PosSale;
use Modules\Sales\Models\PosSaleLine;
use Modules\Sales\Models\PosSession;
use Modules\Sales\Models\PosTerminal;

class Checkout extends Component
{
    public array $cart = [];
    public float $total = 0;
    public $terminal = null;
    public string $notes = '';

    public function mount(): void
    {
        $this->cart  = session()->get('hostel_shop_cart', []);
        $this->total = (float) collect($this->cart)->sum(fn ($item) => $item['qty'] * $item['price']);

        $occupant       = auth('hostel_occupant')->user()->hostelOccupant;
        $this->terminal = PosTerminal::where('hostel_id', $occupant->hostel_id)
            ->where('is_active', true)
            ->first();
    }

    public function placeOrder()
    {
        if (empty($this->cart) || ! $this->terminal) {
            return;
        }

        $user     = auth('hostel_occupant')->user();
        $occupant = $user->hostelOccupant;

        $session = PosSession::where('terminal_id', $this->terminal->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        $notesText = 'Hostel occupant order - ' . $occupant->full_name;
        if ($this->notes) {
            $notesText .= '. Delivery notes: ' . $this->notes;
        }

        $sale = PosSale::create([
            'session_id' => $session?->id,
            'subtotal'   => $this->total,
            'tax'        => 0,
            'total'      => $this->total,
            'notes'      => $notesText,
        ]);

        foreach ($this->cart as $catalogId => $item) {
            PosSaleLine::create([
                'pos_sale_id'     => $sale->id,
                'catalog_item_id' => (int) $catalogId,
                'quantity'        => $item['qty'],
                'unit_price'      => $item['price'],
                'discount_pct'    => 0,
            ]);
        }

        session()->forget('hostel_shop_cart');
        session()->flash('success', 'Order placed! Reference: ' . $sale->reference);

        return $this->redirect(route('hostel_occupant.shop.orders'));
    }

    public function render()
    {
        $cartItems = collect($this->cart)->map(function ($item, $catalogId) {
            return [
                'catalog_id' => $catalogId,
                'name'       => $item['name'],
                'qty'        => $item['qty'],
                'price'      => $item['price'],
                'subtotal'   => $item['qty'] * $item['price'],
            ];
        })->values();

        return view('hostels::livewire.hostel-occupant.shop.checkout', [
            'cartItems' => $cartItems,
        ])->layout('hostels::layouts.occupant');
    }
}
