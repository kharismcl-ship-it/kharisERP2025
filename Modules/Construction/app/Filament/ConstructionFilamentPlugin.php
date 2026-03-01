<?php

namespace Modules\Construction\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Construction\Filament\Resources\ConstructionProjectResource;
use Modules\Construction\Filament\Resources\ContractorResource;

class ConstructionFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'construction';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            ConstructionProjectResource::class,
            ContractorResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
