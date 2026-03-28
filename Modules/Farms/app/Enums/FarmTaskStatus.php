<?php

namespace Modules\Farms\Enums;

use Pcm\FilamentKanban\Concerns\IsKanbanStatus;

enum FarmTaskStatus: string
{
    use IsKanbanStatus;

    case Pending    = 'pending';
    case Assigned   = 'assigned';
    case InProgress = 'in_progress';
    case Completed  = 'completed';

    public function getTitle(): string
    {
        return match ($this) {
            self::Pending    => 'Pending',
            self::Assigned   => 'Assigned',
            self::InProgress => 'In Progress',
            self::Completed  => 'Completed',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending    => 'gray',
            self::Assigned   => 'info',
            self::InProgress => 'warning',
            self::Completed  => 'success',
        };
    }
}