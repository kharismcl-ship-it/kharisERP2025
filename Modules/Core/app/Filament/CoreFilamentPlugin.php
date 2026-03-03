<?php

namespace Modules\Core\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Core\Filament\Pages\ErpAnalyticsDashboard;
use Modules\Core\Filament\Resources\AutomationLogResource;
use Modules\Core\Filament\Resources\AutomationSettingResource;

class CoreFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'core';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            AutomationSettingResource::class,
            AutomationLogResource::class,
        ]);

        $panel->pages([
            ErpAnalyticsDashboard::class,
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
