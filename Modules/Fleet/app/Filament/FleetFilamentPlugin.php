<?php

namespace Modules\Fleet\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Fleet\Filament\Pages\FleetDashboard;
use Modules\Fleet\Filament\Resources\DriverAssignmentResource;
use Modules\Fleet\Filament\Resources\FuelLogResource;
use Modules\Fleet\Filament\Resources\MaintenanceRecordResource;
use Modules\Fleet\Filament\Resources\TripLogResource;
use Modules\Fleet\Filament\Resources\VehicleResource;

class FleetFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'fleet';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                FleetDashboard::class,
            ])
            ->resources([
                VehicleResource::class,
                MaintenanceRecordResource::class,
                FuelLogResource::class,
                TripLogResource::class,
                DriverAssignmentResource::class,
            ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
