<?php

namespace Modules\Hostels\Enums;

enum AssignmentStatus: string
{
    case ACTIVE = 'active';
    case REMOVED = 'removed';
    case DAMAGED = 'damaged';
    case MAINTENANCE = 'maintenance';
    case LOST = 'lost';
    case DECOMMISSIONED = 'decommissioned';
    case RESERVED = 'reserved';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::REMOVED => 'Removed',
            self::DAMAGED => 'Damaged',
            self::MAINTENANCE => 'In Maintenance',
            self::LOST => 'Lost',
            self::DECOMMISSIONED => 'Decommissioned',
            self::RESERVED => 'Reserved',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::REMOVED => 'gray',
            self::DAMAGED => 'danger',
            self::MAINTENANCE => 'warning',
            self::LOST => 'danger',
            self::DECOMMISSIONED => 'secondary',
            self::RESERVED => 'info',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isProblematic(): bool
    {
        return in_array($this, [self::DAMAGED, self::MAINTENANCE, self::LOST]);
    }

    public static function activeStatuses(): array
    {
        return [self::ACTIVE->value, self::RESERVED->value];
    }

    public static function inactiveStatuses(): array
    {
        return [self::REMOVED->value, self::DAMAGED->value, self::MAINTENANCE->value, self::LOST->value, self::DECOMMISSIONED->value];
    }
}
