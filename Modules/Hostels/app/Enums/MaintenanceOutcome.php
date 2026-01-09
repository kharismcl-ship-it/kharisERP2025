<?php

namespace Modules\Hostels\Enums;

enum MaintenanceOutcome: string
{
    case RESOLVED = 'resolved';
    case PARTIALLY_RESOLVED = 'partially_resolved';
    case NOT_RESOLVED = 'not_resolved';
    case DEFERRED = 'deferred';

    public function label(): string
    {
        return match ($this) {
            self::RESOLVED => 'Resolved',
            self::PARTIALLY_RESOLVED => 'Partially Resolved',
            self::NOT_RESOLVED => 'Not Resolved',
            self::DEFERRED => 'Deferred',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::RESOLVED => 'green',
            self::PARTIALLY_RESOLVED => 'orange',
            self::NOT_RESOLVED => 'red',
            self::DEFERRED => 'gray',
        };
    }
}
