<?php

namespace Modules\ProcurementInventory\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\ProcurementInventory\Filament\Vendor\Pages\VendorDashboardPage;
use Modules\ProcurementInventory\Filament\Vendor\Resources\VendorAsnResource;
use Modules\ProcurementInventory\Filament\Vendor\Resources\VendorPurchaseOrderResource;

class VendorPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'vendor-portal';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            VendorPurchaseOrderResource::class,
            VendorAsnResource::class,
        ]);

        $panel->pages([
            VendorDashboardPage::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
