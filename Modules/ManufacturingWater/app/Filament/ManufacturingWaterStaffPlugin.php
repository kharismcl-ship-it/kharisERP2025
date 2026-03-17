<?php

namespace Modules\ManufacturingWater\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\ManufacturingWater\Filament\Resources\Staff\MyWaterTestResource;

class ManufacturingWaterStaffPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'manufacturing-water-staff';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MyWaterTestResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
