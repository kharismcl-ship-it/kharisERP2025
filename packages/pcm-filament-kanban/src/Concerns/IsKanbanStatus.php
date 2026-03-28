<?php

namespace Pcm\FilamentKanban\Concerns;

use Illuminate\Support\Collection;

trait IsKanbanStatus
{
    public static function statuses(): Collection
    {
        return collect(static::kanbanCases())
            ->map(function (self $item) {
                return [
                    'id'    => $item->getId(),
                    'title' => $item->getTitle(),
                    'color' => $item->getColor(),
                ];
            });
    }

    public static function kanbanCases(): array
    {
        return static::cases();
    }

    public function getId(): string
    {
        return $this->value;
    }

    public function getTitle(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }

    // v4 ADDITION: optional color per status (used by kanban-header view)
    public function getColor(): string
    {
        return 'primary';
    }
}