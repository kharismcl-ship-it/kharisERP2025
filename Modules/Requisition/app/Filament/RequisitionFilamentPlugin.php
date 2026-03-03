<?php

namespace Modules\Requisition\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Requisition\Filament\Resources\RequisitionResource;

class RequisitionFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'requisition';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            RequisitionResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
