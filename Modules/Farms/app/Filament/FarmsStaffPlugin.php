<?php

namespace Modules\Farms\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Farms\Filament\Resources\Staff\MyFarmAttendanceResource;
use Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource;
use Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource;
use Modules\Farms\Filament\Resources\Staff\MyFarmTaskResource;

class FarmsStaffPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'farms-staff';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MyFarmTaskResource::class,
            MyFarmDailyReportResource::class,
            MyFarmAttendanceResource::class,
            MyFarmRequestResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
