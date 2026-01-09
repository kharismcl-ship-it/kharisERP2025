<?php

namespace Modules\Hostels\Enums;

enum MaintenanceType: string
{
    case PREVENTIVE = 'preventive';
    case CORRECTIVE = 'corrective';
    case EMERGENCY = 'emergency';
    case ROUTINE = 'routine';

    public function label(): string
    {
        return match ($this) {
            self::PREVENTIVE => 'Preventive',
            self::CORRECTIVE => 'Corrective',
            self::EMERGENCY => 'Emergency',
            self::ROUTINE => 'Routine',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PREVENTIVE => 'Scheduled maintenance to prevent issues',
            self::CORRECTIVE => 'Repair or fix existing issues',
            self::EMERGENCY => 'Urgent maintenance requiring immediate attention',
            self::ROUTINE => 'Regular maintenance tasks',
        };
    }
}
