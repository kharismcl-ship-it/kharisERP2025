<?php

namespace Modules\Construction\Filament\Pages;

use App\Filament\Pages\KanbanPage;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Database\Eloquent\Collection;
use Modules\Construction\Models\ConstructionProject;
use Modules\Construction\Models\ProjectTask;

class ProjectTaskKanban extends KanbanPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 13;

    protected static ?string $navigationLabel = 'Task Board';

    use HasPageShield;

    public ?int $selectedProjectId = null;

    public function getKanbanStatuses(): array
    {
        return [
            ['key' => 'pending',     'label' => 'Pending',     'border_class' => 'border-gray-400',   'dot_color' => 'bg-gray-400'],
            ['key' => 'in_progress', 'label' => 'In Progress', 'border_class' => 'border-yellow-400', 'dot_color' => 'bg-yellow-400'],
            ['key' => 'blocked',     'label' => 'Blocked',     'border_class' => 'border-red-400',    'dot_color' => 'bg-red-500'],
            ['key' => 'completed',   'label' => 'Completed',   'border_class' => 'border-green-500',  'dot_color' => 'bg-green-500'],
        ];
    }

    public function getKanbanRecords(string $status): Collection
    {
        return ProjectTask::query()
            ->with(['project', 'contractor'])
            ->where('status', $status)
            ->when($this->selectedProjectId, fn ($q) =>
                $q->where('construction_project_id', $this->selectedProjectId)
            )
            ->when($this->search, fn ($q) =>
                $q->where('name', 'like', "%{$this->search}%")
            )
            ->orderByDesc('priority')
            ->orderBy('due_date')
            ->get();
    }

    protected function onCardMoved(int|string $recordId, string $newStatus): void
    {
        $validKeys = array_column($this->getKanbanStatuses(), 'key');
        if (! in_array($newStatus, $validKeys, true)) {
            return;
        }

        $task   = ProjectTask::findOrFail($recordId);
        $update = ['status' => $newStatus];

        if ($newStatus === 'completed' && ! $task->completed_at) {
            $update['completed_at'] = now();
        }

        $task->update($update);
    }

    public function getCardView(): string
    {
        return 'construction::filament.kanban.task-card';
    }

    public function getKanbanFilterBarView(): ?array
    {
        return [
            'view'  => 'construction::filament.kanban.project-filter-bar',
            'props' => ['projects' => ConstructionProject::pluck('name', 'id')->toArray()],
        ];
    }
}
