<?php

namespace Modules\ManufacturingWater\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\ManufacturingWater\Filament\Pages\MwDashboard;
use Modules\ManufacturingWater\Filament\Resources\MwChemicalUsageResource;
use Modules\ManufacturingWater\Filament\Resources\MwDistributionRecordResource;
use Modules\ManufacturingWater\Filament\Resources\MwPlantResource;
use Modules\ManufacturingWater\Filament\Resources\MwTankLevelResource;
use Modules\ManufacturingWater\Filament\Resources\MwTreatmentStageResource;
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
            MwTreatmentStageResource::class,
            MwTankLevelResource::class,
            MwChemicalUsageResource::class,
        ]);

        $panel->pages([MwDashboard::class]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
