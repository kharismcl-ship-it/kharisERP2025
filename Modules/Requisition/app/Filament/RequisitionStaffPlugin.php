<?php

namespace Modules\Requisition\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource;

class RequisitionStaffPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'requisition-staff';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MyRequisitionResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
