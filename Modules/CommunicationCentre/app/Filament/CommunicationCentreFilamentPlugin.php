<?php

namespace Modules\CommunicationCentre\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\CommunicationCentre\Filament\Resources\CommMessageResource;
use Modules\CommunicationCentre\Filament\Resources\CommPreferenceResource;
use Modules\CommunicationCentre\Filament\Resources\CommProviderConfigResource;
use Modules\CommunicationCentre\Filament\Resources\CommTemplateResource;

class CommunicationCentreFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'communicationcentre';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CommProviderConfigResource::class,
            CommMessageResource::class,
            CommPreferenceResource::class,
            CommTemplateResource::class,
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
