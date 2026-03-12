<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Books;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Hostels\Models\HostelBook;

class Index extends Component
{
    use WithPagination;

    public string $search     = '';
    public string $typeFilter = '';
    public int $hostelId;
    public array $cart = []; // [bookId => qty]

    public function mount(): void
    {
        $this->hostelId = auth('hostel_occupant')->user()->hostelOccupant->hostel_id;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function addToCart(int $bookId): void
    {
        $this->cart[$bookId] = ($this->cart[$bookId] ?? 0) + 1;
    }

    public function removeFromCart(int $bookId): void
    {
        if (isset($this->cart[$bookId])) {
            if ($this->cart[$bookId] > 1) {
                $this->cart[$bookId]--;
            } else {
                unset($this->cart[$bookId]);
            }
        }
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            return;
        }

        session()->put('hostel_books_cart', $this->cart);

        return $this->redirect(route('hostel_occupant.books.checkout'));
    }

    public function getCartTotalProperty(): float
    {
        return (float) collect($this->cart)->reduce(function ($carry, $qty, $id) {
            $book = HostelBook::find($id);
            return $carry + ($qty * (float) ($book?->price ?? 0));
        }, 0.0);
    }

    public function getCartCountProperty(): int
    {
        return (int) array_sum($this->cart);
    }

    public function render()
    {
        $books = HostelBook::where('is_active', true)
            ->where(function ($q) {
                $q->where('hostel_id', $this->hostelId)
                  ->orWhere('is_globally_available', true);
            })
            ->when($this->search, fn ($q) => $q->where(function ($sq) {
                $sq->where('title', 'like', '%' . $this->search . '%')
                   ->orWhere('author', 'like', '%' . $this->search . '%');
            }))
            ->when($this->typeFilter, fn ($q) => $q->where('book_type', $this->typeFilter))
            ->paginate(12);

        return view('hostels::livewire.hostel-occupant.books.index', [
            'books'      => $books,
            'cartTotal'  => $this->cartTotal,
            'cartCount'  => $this->cartCount,
        ])->layout('hostels::layouts.occupant');
    }
}
