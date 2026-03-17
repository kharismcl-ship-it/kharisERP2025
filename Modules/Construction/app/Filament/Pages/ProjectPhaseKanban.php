<?php

namespace Modules\Construction\Filament\Pages;

use App\Filament\Pages\KanbanPage;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Database\Eloquent\Collection;
use Modules\Construction\Models\ConstructionProject;
use Modules\Construction\Models\ProjectPhase;

class ProjectPhaseKanban extends KanbanPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 14;

    protected static ?string $navigationLabel = 'Phase Board';

    use HasPageShield;

    public ?int $selectedProjectId = null;

    public function getKanbanStatuses(): array
    {
        return [
            ['key' => 'pending',     'label' => 'Pending',     'border_class' => 'border-gray-400',   'dot_color' => 'bg-gray-400'],
            ['key' => 'in_progress', 'label' => 'In Progress', 'border_class' => 'border-yellow-400', 'dot_color' => 'bg-yellow-400'],
            ['key' => 'on_hold',     'label' => 'On Hold',     'border_class' => 'border-orange-400', 'dot_color' => 'bg-orange-400'],
            ['key' => 'completed',   'label' => 'Completed',   'border_class' => 'border-green-500',  'dot_color' => 'bg-green-500'],
        ];
    }

    public function getKanbanRecords(string $status): Collection
    {
        return ProjectPhase::query()
            ->with('project')
            ->where('status', $status)
            ->when($this->selectedProjectId, fn ($q) =>
                $q->where('construction_project_id', $this->selectedProjectId)
            )
            ->when($this->search, fn ($q) =>
                $q->where('name', 'like', "%{$this->search}%")
            )
            ->orderBy('order')
            ->get();
    }

    protected function onCardMoved(int|string $recordId, string $newStatus): void
    {
        $validKeys = array_column($this->getKanbanStatuses(), 'key');
        if (! in_array($newStatus, $validKeys, true)) {
            return;
        }

        ProjectPhase::findOrFail($recordId)->update(['status' => $newStatus]);
    }

    public function getCardView(): string
    {
        return 'construction::filament.kanban.phase-card';
    }

    public function getKanbanFilterBarView(): ?array
    {
        return [
            'view'  => 'construction::filament.kanban.project-filter-bar',
            'props' => ['projects' => ConstructionProject::pluck('name', 'id')->toArray()],
        ];
    }
}
