<?php

namespace Modules\ManufacturingPaper\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\ManufacturingPaper\Filament\Resources\Staff\MyProductionBatchResource;

class ManufacturingPaperStaffPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'manufacturing-paper-staff';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MyProductionBatchResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
