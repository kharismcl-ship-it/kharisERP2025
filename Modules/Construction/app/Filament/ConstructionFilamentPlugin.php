<?php

namespace Modules\Construction\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Construction\Filament\Pages\ProjectPhaseKanban;
use Modules\Construction\Filament\Pages\ProjectTaskKanban;
use Modules\Construction\Filament\Resources\ConstructionDocumentResource;
use Modules\Construction\Filament\Resources\ConstructionProjectResource;
use Modules\Construction\Filament\Resources\ConstructionWorkerResource;
use Modules\Construction\Filament\Resources\ContractorRequestResource;
use Modules\Construction\Filament\Resources\ContractorResource;
use Modules\Construction\Filament\Resources\MaterialUsageResource;
use Modules\Construction\Filament\Resources\MonitoringReportResource;
use Modules\Construction\Filament\Resources\ProjectBudgetItemResource;
use Modules\Construction\Filament\Resources\ProjectPhaseResource;
use Modules\Construction\Filament\Resources\ProjectTaskResource;
use Modules\Construction\Filament\Resources\SiteMonitorResource;
use Modules\Construction\Filament\Resources\WorkerAttendanceResource;

class ConstructionFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'construction';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            ConstructionProjectResource::class,
            ContractorResource::class,
            ProjectPhaseResource::class,
            ProjectTaskResource::class,
            MaterialUsageResource::class,
            ProjectBudgetItemResource::class,
            // Phase 2
            ConstructionWorkerResource::class,
            WorkerAttendanceResource::class,
            SiteMonitorResource::class,
            MonitoringReportResource::class,
            ConstructionDocumentResource::class,
            ContractorRequestResource::class,
        ]);

        $panel->pages([
            ProjectTaskKanban::class,
            ProjectPhaseKanban::class,
        ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}
