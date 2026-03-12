<?php

namespace Modules\Farms\Http\Livewire\Requests;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmRequest;

class Index extends Component
{
    use WithPagination;

    public Farm $farm;

    public string $statusFilter = '';

    public bool $showActionModal = false;

    public ?int $actionRequestId = null;

    public string $actionType = '';

    public string $rejectionReason = '';

    public function mount(Farm $farm): void
    {
        $this->farm = $farm;
    }

    public function getRequestsProperty()
    {
        return FarmRequest::with('items')
            ->where('farm_id', $this->farm->id)
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(20);
    }

    public function openAction(int $requestId, string $action): void
    {
        $this->actionRequestId = $requestId;
        $this->actionType = $action;
        $this->rejectionReason = '';
        $this->showActionModal = true;
    }

    public function confirmAction(): void
    {
        $request = FarmRequest::findOrFail($this->actionRequestId);

        if ($this->actionType === 'approve') {
            $request->update([
                'status'      => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
            $this->dispatch('notify', type: 'success', message: 'Request approved.');
        } elseif ($this->actionType === 'reject') {
            $request->update([
                'status'           => 'rejected',
                'rejection_reason' => $this->rejectionReason,
            ]);
            $this->dispatch('notify', type: 'success', message: 'Request rejected.');
        }

        $this->showActionModal = false;
        $this->actionRequestId = null;
    }

    public function render()
    {
        return view('farms::livewire.requests.index', [
            'requests' => $this->requests,
        ])->layout('farms::layouts.app');
    }
}
