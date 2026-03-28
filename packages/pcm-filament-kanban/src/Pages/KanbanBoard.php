<?php

namespace Pcm\FilamentKanban\Pages;

use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Pcm\FilamentKanban\Concerns\HasEditRecordModal;
use Pcm\FilamentKanban\Concerns\HasStatusChange;
use UnitEnum;

class KanbanBoard extends Page
{
    use HasEditRecordModal;
    use HasStatusChange;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    // v4 FIX: $view must be non-static on Page subclasses
    protected string $view = 'pcm-filament-kanban::kanban-board';

    // v4 FIX: all view references must be non-static
    protected string $headerView = 'pcm-filament-kanban::kanban-header';

    protected string $recordView = 'pcm-filament-kanban::kanban-record';

    protected string $statusView = 'pcm-filament-kanban::kanban-status';

    protected string $scriptsView = 'pcm-filament-kanban::kanban-scripts';

    protected static string $model;

    protected static string $statusEnum;

    protected static string $recordTitleAttribute = 'title';

    protected static string $recordStatusAttribute = 'status';

    protected function statuses(): Collection
    {
        return static::$statusEnum::statuses();
    }

    protected function records(): Collection
    {
        return $this->getEloquentQuery()
            ->when(method_exists(static::$model, 'scopeOrdered'), fn ($query) => $query->ordered())
            ->get();
    }

    protected function getViewData(): array
    {
        $records = $this->records();
        $statuses = $this->statuses()
            ->map(function ($status) use ($records) {
                $status['records'] = $this->filterRecordsByStatus($records, $status);

                return $status;
            });

        return [
            'statuses' => $statuses,
        ];
    }

    protected function filterRecordsByStatus(Collection $records, array $status): array
    {
        $statusIsCastToEnum = $records->first()?->getAttribute(static::$recordStatusAttribute) instanceof UnitEnum;

        $filter = $statusIsCastToEnum
            ? static::$statusEnum::from($status['id'])
            : $status['id'];

        return $records->where(static::$recordStatusAttribute, $filter)->all();
    }

    protected function getEloquentQuery(): Builder
    {
        return static::$model::query();
    }
}