<?php

namespace Modules\ManufacturingWater\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\ManufacturingWater\Filament\Resources\MwDistributionRecordResource;
use Modules\ManufacturingWater\Filament\Resources\MwPlantResource;
use Modules\ManufacturingWater\Filament\Resources\MwWaterTestRecordResource;

class ManufacturingWaterFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'manufacturing-water';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MwPlantResource::class,
            MwDistributionRecordResource::class,
            MwWaterTestRecordResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
