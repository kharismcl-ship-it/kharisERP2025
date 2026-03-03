<?php

namespace Modules\Sales\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Sales\Filament\Pages\SalesDashboard;
use Modules\Sales\Filament\Pages\SalesOpportunityKanban;
use Modules\Sales\Filament\Resources\CatalogItemResource;
use Modules\Sales\Filament\Resources\ContactResource;
use Modules\Sales\Filament\Resources\DiningOrderResource;
use Modules\Sales\Filament\Resources\DiningTableResource;
use Modules\Sales\Filament\Resources\LeadResource;
use Modules\Sales\Filament\Resources\OpportunityResource;
use Modules\Sales\Filament\Resources\OrganizationResource;
use Modules\Sales\Filament\Resources\PosSaleResource;
use Modules\Sales\Filament\Resources\PosSessionResource;
use Modules\Sales\Filament\Resources\PosTerminalResource;
use Modules\Sales\Filament\Resources\PriceListResource;
use Modules\Sales\Filament\Resources\QuotationResource;
use Modules\Sales\Filament\Resources\SalesOrderResource;

class SalesFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'sales';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            // CRM
            LeadResource::class,
            ContactResource::class,
            OrganizationResource::class,

            // Catalog
            CatalogItemResource::class,
            PriceListResource::class,

            // Pipeline
            OpportunityResource::class,

            // Orders
            QuotationResource::class,
            SalesOrderResource::class,

            // POS
            PosTerminalResource::class,
            PosSessionResource::class,
            PosSaleResource::class,

            // Restaurant
            DiningTableResource::class,
            DiningOrderResource::class,
        ]);

        $panel->pages([
            SalesDashboard::class,
            SalesOpportunityKanban::class,
        ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
