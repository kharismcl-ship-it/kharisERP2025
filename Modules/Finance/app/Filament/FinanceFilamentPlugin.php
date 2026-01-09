<?php

namespace Modules\Finance\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Finance\Filament\Resources\AccountResource;
use Modules\Finance\Filament\Resources\InvoiceLineResource;
use Modules\Finance\Filament\Resources\InvoiceResource;
use Modules\Finance\Filament\Resources\JournalEntryResource;
use Modules\Finance\Filament\Resources\JournalLineResource;
use Modules\Finance\Filament\Resources\PaymentResource;
use Modules\Finance\Filament\Resources\ReceiptResource;

class FinanceFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'finance';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            ReceiptResource::class,
            AccountResource::class,
            InvoiceLineResource::class,
            JournalLineResource::class,
            InvoiceResource::class,
            PaymentResource::class,
            JournalEntryResource::class,
        ]);

        $panel->pages([
            // Register all Filament Pages Class
        ]);

        $panel->navigationItems([
            // Register navigation items
            // NavigationItem::make('Dashboard')
            //     ->url('/dashboard')
            //     ->icon('heroicon-o-home'),
        ]);
    }

    public function boot(Panel $panel): void
    {
        // Boot logic here
    }
}
