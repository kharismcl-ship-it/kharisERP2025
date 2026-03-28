<?php

namespace Modules\Farms\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Modules\Farms\Models\FarmIotDevice;

class IotSensorDashboardPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-signal';

    protected static string|\UnitEnum|null $navigationGroup = 'Precision Agriculture';

    protected static ?string $navigationLabel = 'Sensor Dashboard';

    protected static ?int $navigationSort = 2;

    protected string $view = 'farms::filament.pages.iot-sensor-dashboard';

    public array $devices = [];

    public function mount(): void
    {
        $this->loadDevices();
    }

    public function loadDevices(): void
    {
        $companyId = Filament::getTenant()?->id;

        $this->devices = FarmIotDevice::where('company_id', $companyId)
            ->with(['farm', 'readings' => fn ($q) => $q->orderByDesc('recorded_at')->limit(1)])
            ->get()
            ->map(fn (FarmIotDevice $d) => [
                'id'              => $d->id,
                'name'            => $d->device_name,
                'type'            => $d->device_type,
                'farm'            => $d->farm?->name,
                'status'          => $d->status,
                'last_value'      => $d->last_reading_value,
                'last_unit'       => $d->readings->first()?->reading_unit ?? '',
                'last_reading_at' => $d->last_reading_at?->diffForHumans(),
                'battery_pct'     => $d->battery_pct,
                'is_online'       => $d->isOnline(),
            ])
            ->toArray();
    }
}