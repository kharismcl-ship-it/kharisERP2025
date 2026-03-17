<?php

namespace Modules\ClientService\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\ClientService\Filament\Resources\Staff\MyVisitorResource;

class ClientServiceStaffPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'clientservice-staff';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MyVisitorResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
