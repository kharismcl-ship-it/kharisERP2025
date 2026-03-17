<?php

namespace Modules\Hostels\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Hostels\Filament\Resources\Staff\MyHousekeepingScheduleResource;
use Modules\Hostels\Filament\Resources\Staff\MyIncidentResource;
use Modules\Hostels\Filament\Resources\Staff\MyMaintenanceRequestResource;
use Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource;

class HostelsStaffPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'hostels-staff';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MyVisitorLogResource::class,
            MyIncidentResource::class,
            MyMaintenanceRequestResource::class,
            MyHousekeepingScheduleResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
