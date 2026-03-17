<?php

namespace Modules\Construction\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Construction\Filament\Resources\Staff\MyWorkerAttendanceResource;

class ConstructionStaffPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'construction-staff';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MyWorkerAttendanceResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
