<?php

namespace Modules\ITSupport\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\ITSupport\Filament\Resources\ItActivityResource;
use Modules\ITSupport\Filament\Resources\ItRequestResource;
use Modules\ITSupport\Filament\Resources\ItTrainingSessionResource;

class ITSupportFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'itsupport';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            ItRequestResource::class,
            ItTrainingSessionResource::class,
            ItActivityResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
