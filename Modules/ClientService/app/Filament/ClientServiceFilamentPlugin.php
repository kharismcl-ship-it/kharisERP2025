<?php

namespace Modules\ClientService\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\ClientService\Filament\Resources\CsAttendanceResource;
use Modules\ClientService\Filament\Resources\CsVisitorBadgeResource;
use Modules\ClientService\Filament\Resources\CsVisitorProfileResource;
use Modules\ClientService\Filament\Resources\CsVisitorResource;

class ClientServiceFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'clientservice';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CsAttendanceResource::class,
            CsVisitorResource::class,
            CsVisitorProfileResource::class,
            CsVisitorBadgeResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
