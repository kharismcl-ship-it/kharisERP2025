<?php

namespace Modules\Farms\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Farms\Filament\Pages\FarmDashboard;
use Modules\Farms\Filament\Resources\CropActivityResource;
use Modules\Farms\Filament\Resources\CropCycleResource;
use Modules\Farms\Filament\Resources\CropScoutingResource;
use Modules\Farms\Filament\Resources\CropVarietyResource;
use Modules\Farms\Filament\Resources\FarmBudgetResource;
use Modules\Farms\Filament\Resources\FarmEquipmentResource;
use Modules\Farms\Filament\Resources\FarmExpenseResource;
use Modules\Farms\Filament\Resources\FarmProduceInventoryResource;
use Modules\Farms\Filament\Resources\FarmResource;
use Modules\Farms\Filament\Resources\FarmSaleResource;
use Modules\Farms\Filament\Resources\FarmTaskResource;
use Modules\Farms\Filament\Resources\FarmWeatherLogResource;
use Modules\Farms\Filament\Resources\FarmWorkerResource;
use Modules\Farms\Filament\Resources\LivestockBatchResource;
use Modules\Farms\Filament\Resources\LivestockEventResource;
use Modules\Farms\Filament\Resources\LivestockHealthRecordResource;
use Modules\Farms\Filament\Resources\SoilTestRecordResource;

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
            FarmWorkerResource::class,
            FarmTaskResource::class,
            FarmSaleResource::class,
            FarmBudgetResource::class,
            FarmExpenseResource::class,
            // Phase 6 — Farmbrite parity
            FarmProduceInventoryResource::class,
            LivestockEventResource::class,
            FarmEquipmentResource::class,
            FarmWeatherLogResource::class,
            SoilTestRecordResource::class,
            CropVarietyResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        $panel->pages([FarmDashboard::class]);
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
