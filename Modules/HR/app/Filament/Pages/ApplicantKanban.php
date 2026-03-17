<?php

namespace Modules\HR\Filament\Pages;

use App\Filament\Pages\KanbanPage;
use Illuminate\Database\Eloquent\Collection;
use Modules\HR\Models\Applicant;

class ApplicantKanban extends KanbanPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    protected static string|\UnitEnum|null $navigationGroup = 'Recruitment';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Recruitment Board';

    public function getKanbanStatuses(): array
    {
        return [
            ['key' => 'applied',             'label' => 'Applied',             'border_class' => 'border-blue-400',   'dot_color' => 'bg-blue-400'],
            ['key' => 'shortlisted',         'label' => 'Shortlisted',         'border_class' => 'border-cyan-500',   'dot_color' => 'bg-cyan-500'],
            ['key' => 'interview_scheduled', 'label' => 'Interview Scheduled', 'border_class' => 'border-purple-400', 'dot_color' => 'bg-purple-400'],
            ['key' => 'interviewed',         'label' => 'Interviewed',         'border_class' => 'border-indigo-400', 'dot_color' => 'bg-indigo-400'],
            ['key' => 'offered',             'label' => 'Offered',             'border_class' => 'border-teal-500',   'dot_color' => 'bg-teal-500'],
            ['key' => 'hired',               'label' => 'Hired',               'border_class' => 'border-green-500',  'dot_color' => 'bg-green-500'],
            ['key' => 'rejected',            'label' => 'Rejected',            'border_class' => 'border-red-400',    'dot_color' => 'bg-red-400'],
            ['key' => 'withdrawn',           'label' => 'Withdrawn',           'border_class' => 'border-gray-400',   'dot_color' => 'bg-gray-400'],
        ];
    }

    public function getKanbanRecords(string $status): Collection
    {
        return Applicant::query()
            ->with('jobVacancy')
            ->where('status', $status)
            ->when($this->search, fn ($q) =>
                $q->where(fn ($sub) =>
                    $sub->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhereHas('jobVacancy', fn ($jq) => $jq->where('title', 'like', "%{$this->search}%"))
                )
            )
            ->orderByDesc('applied_date')
            ->get();
    }

    protected function onCardMoved(int|string $recordId, string $newStatus): void
    {
        if (! array_key_exists($newStatus, Applicant::STATUSES)) {
            return;
        }

        Applicant::findOrFail($recordId)->update(['status' => $newStatus]);
    }

    public function getCardView(): string
    {
        return 'hr::filament.kanban.applicant-card';
    }
}
