<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Shop;

use Livewire\Component;
use Modules\Sales\Models\PosTerminal;
use Modules\Sales\Models\SalesCatalog;

class Index extends Component
{
    public $terminal = null;
    public int $hostelId;
    public array $cart = []; // [catalog_id => ['qty' => n, 'name' => '...', 'price' => 0.00]]
    public string $search = '';

    public function mount(): void
    {
        $occupant        = auth('hostel_occupant')->user()->hostelOccupant;
        $this->hostelId  = $occupant->hostel_id;
        $this->terminal  = PosTerminal::where('hostel_id', $this->hostelId)
            ->where('is_active', true)
            ->first();
    }

    public function addToCart(int $catalogId): void
    {
        if (isset($this->cart[$catalogId])) {
            $this->cart[$catalogId]['qty']++;
        } else {
            $item = SalesCatalog::find($catalogId);
            if ($item) {
                $this->cart[$catalogId] = [
                    'qty'   => 1,
                    'name'  => $item->name,
                    'price' => (float) $item->base_price,
                ];
            }
        }
    }

    public function removeFromCart(int $catalogId): void
    {
        if (isset($this->cart[$catalogId])) {
            if ($this->cart[$catalogId]['qty'] > 1) {
                $this->cart[$catalogId]['qty']--;
            } else {
                unset($this->cart[$catalogId]);
            }
        }
    }

    public function getCartTotalProperty(): float
    {
        return (float) collect($this->cart)->sum(fn ($item) => $item['qty'] * $item['price']);
    }

    public function getCartCountProperty(): int
    {
        return (int) collect($this->cart)->sum('qty');
    }

    public function storeCartAndRedirect()
    {
        session()->put('hostel_shop_cart', $this->cart);

        return $this->redirect(route('hostel_occupant.shop.checkout'));
    }

    public function render()
    {
        $items = $this->terminal
            ? SalesCatalog::where('is_active', true)
                ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->orderBy('source_type')
                ->orderBy('name')
                ->get()
            : collect();

        return view('hostels::livewire.hostel-occupant.shop.index', [
            'items' => $items,
        ])->layout('hostels::layouts.occupant');
    }
}
