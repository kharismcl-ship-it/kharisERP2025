<?php

namespace Modules\Construction\Filament\Resources\SiteMonitorResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Construction\Filament\Resources\SiteMonitorResource;

class ListSiteMonitors extends ListRecords
{
    protected static string $resource = SiteMonitorResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
