<?php

namespace Modules\Farms\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Farms\Filament\Resources\CropActivityResource;
use Modules\Farms\Filament\Resources\CropCycleResource;
use Modules\Farms\Filament\Resources\CropScoutingResource;
use Modules\Farms\Filament\Resources\FarmExpenseResource;
use Modules\Farms\Filament\Resources\FarmResource;
use Modules\Farms\Filament\Resources\LivestockBatchResource;
use Modules\Farms\Filament\Resources\LivestockHealthRecordResource;

class FarmsFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'farms';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            FarmResource::class,
            CropCycleResource::class,
            LivestockBatchResource::class,
            LivestockHealthRecordResource::class,
            CropActivityResource::class,
            CropScoutingResource::class,
            FarmExpenseResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
