<?php

namespace Modules\Construction\Filament\Resources\SiteMonitorResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Construction\Filament\Resources\SiteMonitorResource;

class ViewSiteMonitor extends ViewRecord
{
    protected static string $resource = SiteMonitorResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
