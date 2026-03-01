<?php

namespace Modules\ProcurementInventory\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\ProcurementInventory\Filament\Pages\ProcurementDashboard;
use Modules\ProcurementInventory\Filament\Resources\GoodsReceiptResource;
use Modules\ProcurementInventory\Filament\Resources\ItemCategoryResource;
use Modules\ProcurementInventory\Filament\Resources\ItemResource;
use Modules\ProcurementInventory\Filament\Resources\PurchaseOrderResource;
use Modules\ProcurementInventory\Filament\Resources\StockLevelResource;
use Modules\ProcurementInventory\Filament\Resources\StockMovementResource;
use Modules\ProcurementInventory\Filament\Resources\VendorResource;

class ProcurementInventoryFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'procurement-inventory';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([
            ProcurementDashboard::class,
        ]);

        $panel->resources([
            ItemCategoryResource::class,
            ItemResource::class,
            VendorResource::class,
            PurchaseOrderResource::class,
            GoodsReceiptResource::class,
            StockLevelResource::class,
            StockMovementResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
