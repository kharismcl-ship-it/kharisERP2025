<?php

namespace Modules\Fleet\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Fleet\Filament\Resources\Staff\MyTripLogResource;

class FleetStaffPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'fleet-staff';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MyTripLogResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
