<?php

namespace Modules\Sales\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Sales\Filament\Resources\Staff\MySalesActivityResource;
use Modules\Sales\Filament\Resources\Staff\MyOpportunityResource;

class SalesStaffPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'sales-staff';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MyOpportunityResource::class,
            MySalesActivityResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
