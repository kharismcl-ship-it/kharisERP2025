<?php

namespace Modules\ManufacturingPaper\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\ManufacturingPaper\Filament\Pages\MpDashboard;
use Modules\ManufacturingPaper\Filament\Resources\MpEquipmentLogResource;
use Modules\ManufacturingPaper\Filament\Resources\MpPaperGradeResource;
use Modules\ManufacturingPaper\Filament\Resources\MpPlantResource;
use Modules\ManufacturingPaper\Filament\Resources\MpProductionBatchResource;
use Modules\ManufacturingPaper\Filament\Resources\MpProductionLineResource;
use Modules\ManufacturingPaper\Filament\Resources\MpQualityRecordResource;

class ManufacturingPaperFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'manufacturing-paper';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MpPlantResource::class,
            MpProductionBatchResource::class,
            MpPaperGradeResource::class,
            MpProductionLineResource::class,
            MpQualityRecordResource::class,
            MpEquipmentLogResource::class,
        ]);

        $panel->pages([MpDashboard::class]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
