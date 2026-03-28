<?php

/**
 * RequisitionVendorPlugin — registers Requisition resources in the Vendor Panel.
 *
 * To enable, add to VendorPanelProvider.php:
 *
 *   ->plugins([
 *       ...
 *       \Modules\Requisition\Filament\RequisitionVendorPlugin::make(),
 *   ])
 */

namespace Modules\Requisition\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Requisition\Filament\Vendor\VendorRfqResource;

class RequisitionVendorPlugin implements Plugin
{
    public function getId(): string
    {
        return 'requisition-vendor';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            VendorRfqResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}