<?php

namespace Modules\HR\Filament\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\On;
use Modules\HR\Models\Applicant;

class ApplicantKanban extends Page
{
    protected string $view = 'hr::filament.pages.applicant-kanban';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    protected static string|\UnitEnum|null $navigationGroup = 'Recruitment';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Recruitment Board';

    public function getColumns(): array
    {
        return array_keys(Applicant::STATUSES);
    }

    public function getApplicantsByStatus(string $status): \Illuminate\Database\Eloquent\Collection
    {
        return Applicant::query()
            ->with('jobVacancy')
            ->where('status', $status)
            ->orderByDesc('applied_date')
            ->get();
    }

    #[On('applicant-status-changed')]
    public function handleStatusChanged(int $applicantId, string $newStatus): void
    {
        Applicant::findOrFail($applicantId)->update(['status' => $newStatus]);
    }
}
