<?php

namespace Modules\Farms\Http\Livewire\DailyReports;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmDailyReport;

class Index extends Component
{
    use WithPagination;

    public Farm $farm;

    public string $statusFilter = '';

    public string $dateFilter = '';

    // Review modal
    public bool $showReviewModal = false;

    public ?int $reviewingId = null;

    public string $reviewNotes = '';

    public function mount(Farm $farm): void
    {
        $this->farm = $farm;
    }

    public function getReportsProperty()
    {
        $query = FarmDailyReport::with(['worker'])
            ->where('farm_id', $this->farm->id);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->dateFilter) {
            $query->whereDate('report_date', $this->dateFilter);
        }

        return $query->latest('report_date')->paginate(20);
    }

    public function openReviewModal(int $reportId): void
    {
        $this->reviewingId = $reportId;
        $this->reviewNotes = '';
        $this->showReviewModal = true;
    }

    public function approveReview(): void
    {
        $report = FarmDailyReport::findOrFail($this->reviewingId);

        $report->update([
            'status'      => 'reviewed',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $this->showReviewModal = false;
        $this->dispatch('notify', type: 'success', message: 'Report marked as reviewed.');
    }

    public function render()
    {
        return view('farms::livewire.daily-reports.index', [
            'reports' => $this->reports,
        ])->layout('farms::layouts.app');
    }
}
