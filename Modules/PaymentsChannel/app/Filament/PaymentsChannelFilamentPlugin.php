<?php

namespace Modules\PaymentsChannel\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\PaymentsChannel\Filament\Resources\PayIntentResource;
use Modules\PaymentsChannel\Filament\Resources\PayMethodResource;
use Modules\PaymentsChannel\Filament\Resources\PayProviderConfigResource;
use Modules\PaymentsChannel\Filament\Resources\PayTransactionResource;

class PaymentsChannelFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'paymentschannel';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PayIntentResource::class,
            PayProviderConfigResource::class,
            PayTransactionResource::class,
            PayMethodResource::class,
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
