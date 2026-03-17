<?php

namespace Modules\ITSupport\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\ITSupport\Filament\Resources\Staff\MyItRequestResource;

class ITSupportStaffPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'itsupport-staff';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MyItRequestResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
