<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Restaurant;

use Livewire\Component;
use Modules\Sales\Models\DiningOrder;
use Modules\Sales\Models\DiningOrderItem;
use Modules\Sales\Models\SalesCatalog;
use Modules\Sales\Models\SalesRestaurant;

class Menu extends Component
{
    public $restaurant = null;
    public int $hostelId;
    public array $cart = []; // [catalog_id => quantity]
    public string $activeCategory = 'all';

    public function mount(): void
    {
        $occupant        = auth('hostel_occupant')->user()->hostelOccupant;
        $this->hostelId  = $occupant->hostel_id;
        $this->restaurant = SalesRestaurant::where('hostel_id', $this->hostelId)
            ->where('is_active', true)
            ->first();
    }

    public function addToCart(int $catalogId): void
    {
        $this->cart[$catalogId] = ($this->cart[$catalogId] ?? 0) + 1;
    }

    public function removeFromCart(int $catalogId): void
    {
        if (isset($this->cart[$catalogId])) {
            if ($this->cart[$catalogId] > 1) {
                $this->cart[$catalogId]--;
            } else {
                unset($this->cart[$catalogId]);
            }
        }
    }

    public function placeOrder(): void
    {
        if (empty($this->cart) || ! $this->restaurant) {
            return;
        }

        $user     = auth('hostel_occupant')->user();
        $occupant = $user->hostelOccupant;

        $order = DiningOrder::create([
            'table_id'  => null,
            'status'    => 'open',
            'subtotal'  => 0,
            'tax'       => 0,
            'total'     => 0,
            'notes'     => 'Room delivery - Occupant: ' . $occupant->full_name,
        ]);

        foreach ($this->cart as $catalogId => $qty) {
            $item = SalesCatalog::find($catalogId);
            if ($item) {
                DiningOrderItem::create([
                    'dining_order_id' => $order->id,
                    'catalog_item_id' => $catalogId,
                    'quantity'        => $qty,
                    'unit_price'      => $item->base_price,
                    'status'          => 'pending',
                ]);
            }
        }

        // Reload items for recalculate
        $order->load('items');

        // Manually compute totals since table_id is null
        $subtotal       = $order->items->sum('line_total');
        $taxRate        = $this->restaurant->default_vat_rate ?? 15.0;
        $tax            = round($subtotal * ($taxRate / 100), 2);
        $order->subtotal = $subtotal;
        $order->tax      = $tax;
        $order->total    = round($subtotal + $tax, 2);
        $order->saveQuietly();

        $this->cart = [];

        session()->flash('success', 'Order #' . $order->id . ' placed! Your food will be delivered to your room.');
    }

    public function render()
    {
        $menuItems = $this->restaurant
            ? SalesCatalog::where('is_active', true)
                ->when($this->activeCategory !== 'all', fn ($q) => $q->where('source_type', $this->activeCategory))
                ->where('source_module', 'Restaurant')
                ->orderBy('source_type')
                ->orderBy('name')
                ->get()
            : collect();

        $categories = $this->restaurant
            ? SalesCatalog::where('is_active', true)
                ->where('source_module', 'Restaurant')
                ->distinct()
                ->pluck('source_type')
                ->filter()
                ->values()
            : collect();

        $cartTotal = collect($this->cart)->reduce(function ($carry, $qty, $id) {
            $item = SalesCatalog::find($id);
            return $carry + ($qty * (float) ($item?->base_price ?? 0));
        }, 0.0);

        $cartCount = array_sum($this->cart);

        return view('hostels::livewire.hostel-occupant.restaurant.menu', [
            'menuItems'  => $menuItems,
            'categories' => $categories,
            'cartTotal'  => $cartTotal,
            'cartCount'  => $cartCount,
        ])->layout('hostels::layouts.occupant');
    }
}
