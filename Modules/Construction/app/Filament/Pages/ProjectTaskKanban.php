<?php

namespace Modules\Construction\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Livewire\Attributes\On;
use Modules\Construction\Models\ConstructionProject;
use Modules\Construction\Models\ProjectTask;

class ProjectTaskKanban extends Page
{
    protected string $view = 'construction::filament.pages.project-task-kanban';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 13;

    protected static ?string $navigationLabel = 'Task Board';

    public ?int $selectedProjectId = null;

    public function getColumns(): array
    {
        return [
            ['key' => 'pending',     'label' => 'Pending',     'color' => 'gray'],
            ['key' => 'in_progress', 'label' => 'In Progress', 'color' => 'warning'],
            ['key' => 'blocked',     'label' => 'Blocked',     'color' => 'danger'],
            ['key' => 'completed',   'label' => 'Completed',   'color' => 'success'],
        ];
    }

    public function getTasksByStatus(string $status): \Illuminate\Database\Eloquent\Collection
    {
        $query = ProjectTask::query()
            ->with(['project', 'contractor'])
            ->where('status', $status)
            ->orderByDesc('priority')
            ->orderBy('due_date');

        if ($this->selectedProjectId) {
            $query->where('construction_project_id', $this->selectedProjectId);
        }

        return $query->get();
    }

    public function getProjects(): array
    {
        return ConstructionProject::pluck('name', 'id')->toArray();
    }

    #[On('task-status-changed')]
    public function handleStatusChanged(int $taskId, string $newStatus): void
    {
        $task = ProjectTask::findOrFail($taskId);
        $update = ['status' => $newStatus];

        if ($newStatus === 'completed' && !$task->completed_at) {
            $update['completed_at'] = now();
        }

        $task->update($update);
    }
}
