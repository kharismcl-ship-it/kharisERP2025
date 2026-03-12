<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Books;

use Livewire\Component;
use Modules\Hostels\Models\HostelBook;
use Modules\Hostels\Models\HostelBookOrder;
use Modules\Hostels\Models\HostelBookOrderItem;

class Checkout extends Component
{
    public array $cart  = []; // [bookId => qty]
    public float $total = 0;
    public string $notes = '';

    public function mount(): void
    {
        $this->cart  = session()->get('hostel_books_cart', []);
        $this->total = (float) collect($this->cart)->reduce(function ($carry, $qty, $bookId) {
            $book = HostelBook::find($bookId);
            return $carry + ($qty * (float) ($book?->price ?? 0));
        }, 0.0);
    }

    public function placeOrder()
    {
        if (empty($this->cart)) {
            return;
        }

        $user     = auth('hostel_occupant')->user();
        $occupant = $user->hostelOccupant;

        $order = HostelBookOrder::create([
            'company_id'          => $occupant->company_id,
            'hostel_occupant_id'  => $user->hostel_occupant_id,
            'hostel_id'           => $occupant->hostel_id,
            'subtotal'            => $this->total,
            'total'               => $this->total,
            'status'              => 'pending',
            'notes'               => $this->notes ?: null,
        ]);

        foreach ($this->cart as $bookId => $qty) {
            $book = HostelBook::find($bookId);
            if ($book) {
                HostelBookOrderItem::create([
                    'hostel_book_order_id' => $order->id,
                    'hostel_book_id'       => (int) $bookId,
                    'quantity'             => $qty,
                    'unit_price'           => $book->price,
                    'subtotal'             => $qty * $book->price,
                ]);

                // Decrement stock for physical books
                if ($book->book_type === 'physical' && $book->stock_qty > 0) {
                    $book->decrement('stock_qty', $qty);
                }
            }
        }

        session()->forget('hostel_books_cart');

        // Mark as paid immediately (placeholder — wire to PaymentsChannel in production)
        $order->update(['status' => 'paid', 'paid_at' => now()]);

        session()->flash('success', 'Order ' . $order->reference . ' placed successfully!');

        return $this->redirect(route('hostel_occupant.books.orders'));
    }

    public function render()
    {
        $cartItems = collect($this->cart)->map(function ($qty, $bookId) {
            $book = HostelBook::find($bookId);
            if (! $book) {
                return null;
            }
            return [
                'book'     => $book,
                'qty'      => $qty,
                'subtotal' => $qty * (float) $book->price,
            ];
        })->filter()->values();

        return view('hostels::livewire.hostel-occupant.books.checkout', [
            'cartItems' => $cartItems,
        ])->layout('hostels::layouts.occupant');
    }
}
