<?php

namespace Modules\Farms\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Modules\Farms\Enums\FarmTaskStatus;
use Modules\Farms\Models\FarmTask;
use Modules\Farms\Models\FarmWorker;
use Pcm\FilamentKanban\Pages\KanbanBoard;

class FarmTaskKanbanPage extends KanbanBoard
{
    protected static string $model = FarmTask::class;

    protected static string $statusEnum = FarmTaskStatus::class;

    protected static string $recordTitleAttribute = 'title';

    protected static string $recordStatusAttribute = 'kanban_status';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?string $navigationLabel = 'Task Board';

    protected static ?int $navigationSort = 9;

    protected string $recordView = 'farms::filament.kanban.farm-task-card';

    protected string $editModalTitle = 'Edit Task';

    protected bool $editModalSlideOver = true;

    protected string $editModalWidth = 'lg';

    protected function getEloquentQuery(): Builder
    {
        return FarmTask::query()
            ->where('company_id', Filament::getTenant()?->id)
            ->whereNull('deleted_at')
            ->with(['assignedWorker', 'farm'])
            ->orderBy('sort_order')
            ->orderBy('due_date');
    }

    public function onStatusChanged(int|string $recordId, string $status, array $fromOrderedIds, array $toOrderedIds): void
    {
        $task = FarmTask::find($recordId);

        if (! $task) {
            return;
        }

        $task->kanban_status = $status;

        if ($status === FarmTaskStatus::Completed->value && is_null($task->completed_at)) {
            $task->completed_at = now();
        } elseif ($status !== FarmTaskStatus::Completed->value) {
            $task->completed_at = null;
        }

        $task->save();

        foreach ($toOrderedIds as $i => $id) {
            FarmTask::where('id', $id)->update(['sort_order' => $i]);
        }
    }

    public function onSortChanged(int|string $recordId, string $status, array $orderedIds): void
    {
        foreach ($orderedIds as $i => $id) {
            FarmTask::where('id', $id)->update(['sort_order' => $i]);
        }
    }

    protected function getEditModalFormSchema(null|int|string $recordId): array
    {
        return [
            TextInput::make('title')
                ->required(),

            Select::make('priority')
                ->options([
                    'low'    => 'Low',
                    'medium' => 'Medium',
                    'high'   => 'High',
                    'urgent' => 'Urgent',
                ])
                ->required(),

            Select::make('task_type')
                ->options([
                    'weeding'      => 'Weeding',
                    'spraying'     => 'Spraying',
                    'harvesting'   => 'Harvesting',
                    'irrigation'   => 'Irrigation',
                    'maintenance'  => 'Maintenance',
                    'feeding'      => 'Feeding',
                    'health_check' => 'Health Check',
                    'planting'     => 'Planting',
                    'soil_prep'    => 'Soil Preparation',
                    'scouting'     => 'Scouting',
                    'other'        => 'Other',
                ])
                ->required(),

            DatePicker::make('due_date'),

            Select::make('assigned_to_worker_id')
                ->label('Assign To')
                ->options(fn () => FarmWorker::where('company_id', Filament::getTenant()?->id)
                    ->where('is_active', true)
                    ->get()
                    ->pluck('display_name', 'id'))
                ->searchable()
                ->nullable(),

            Textarea::make('notes')
                ->rows(3),
        ];
    }
}