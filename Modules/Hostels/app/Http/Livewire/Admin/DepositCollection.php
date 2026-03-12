<?php

namespace Modules\Hostels\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Hostels\Models\Deposit;
use Modules\Hostels\Models\Hostel;

class DepositCollection extends Component
{
    use WithPagination;

    public Hostel $hostel;

    public string $statusFilter = 'pending';

    public ?int $refundDepositId = null;

    public float $refundAmount = 0;

    public string $deductionReason = '';

    public bool $showRefundModal = false;

    public function mount(Hostel $hostel): void
    {
        $this->hostel = $hostel;
    }

    public function markCollected(int $depositId): void
    {
        $deposit = Deposit::findOrFail($depositId);

        try {
            $deposit->markAsCollected();
            $this->dispatch('notify', type: 'success', message: 'Deposit marked as collected.');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function openRefundModal(int $depositId): void
    {
        $deposit = Deposit::findOrFail($depositId);
        $this->refundDepositId = $depositId;
        $this->refundAmount = $deposit->calculateRefundAmount();
        $this->deductionReason = '';
        $this->showRefundModal = true;
    }

    public function processRefund(): void
    {
        $this->validate([
            'refundAmount' => 'required|numeric|min:0',
        ]);

        $deposit = Deposit::findOrFail($this->refundDepositId);

        try {
            $deductions = $deposit->amount - $this->refundAmount;
            $deposit->processRefund($this->refundAmount, max(0, $deductions), $this->deductionReason ?: null);

            $this->showRefundModal = false;
            $this->refundDepositId = null;
            $this->dispatch('notify', type: 'success', message: 'Refund processed successfully.');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function getDepositsProperty()
    {
        return Deposit::with(['booking', 'occupant'])
            ->where('hostel_id', $this->hostel->id)
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(20);
    }

    public function render()
    {
        return view('hostels::livewire.admin.deposit-collection', [
            'deposits' => $this->deposits,
        ])->layout('layouts.app');
    }
}
