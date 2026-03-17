<?php

namespace Modules\Finance\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Finance\Filament\Resources\Staff\MyFixedAssetResource;

class FinanceStaffPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'finance-staff';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MyFixedAssetResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
