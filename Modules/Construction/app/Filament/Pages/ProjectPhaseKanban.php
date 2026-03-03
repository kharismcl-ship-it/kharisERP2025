<?php

namespace Modules\Construction\Filament\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\On;
use Modules\Construction\Models\ConstructionProject;
use Modules\Construction\Models\ProjectPhase;

class ProjectPhaseKanban extends Page
{
    protected string $view = 'construction::filament.pages.project-phase-kanban';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 14;

    protected static ?string $navigationLabel = 'Phase Board';

    public ?int $selectedProjectId = null;

    public function getColumns(): array
    {
        return [
            ['key' => 'pending',     'label' => 'Pending'],
            ['key' => 'in_progress', 'label' => 'In Progress'],
            ['key' => 'on_hold',     'label' => 'On Hold'],
            ['key' => 'completed',   'label' => 'Completed'],
        ];
    }

    public function getPhasesByStatus(string $status): \Illuminate\Database\Eloquent\Collection
    {
        $query = ProjectPhase::query()
            ->with('project')
            ->where('status', $status)
            ->orderBy('order');

        if ($this->selectedProjectId) {
            $query->where('construction_project_id', $this->selectedProjectId);
        }

        return $query->get();
    }

    public function getProjects(): array
    {
        return ConstructionProject::pluck('name', 'id')->toArray();
    }

    #[On('phase-status-changed')]
    public function handleStatusChanged(int $phaseId, string $newStatus): void
    {
        ProjectPhase::findOrFail($phaseId)->update(['status' => $newStatus]);
    }
}
