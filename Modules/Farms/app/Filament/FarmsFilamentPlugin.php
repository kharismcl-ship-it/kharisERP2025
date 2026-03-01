<?php

namespace Modules\Farms\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Farms\Filament\Resources\FarmExpenseResource;
use Modules\Farms\Filament\Resources\FarmResource;

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
            FarmExpenseResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
