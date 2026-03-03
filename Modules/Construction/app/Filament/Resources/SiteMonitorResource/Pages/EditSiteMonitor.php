<?php

namespace Modules\Construction\Filament\Resources\SiteMonitorResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Construction\Filament\Resources\SiteMonitorResource;

class EditSiteMonitor extends EditRecord
{
    protected static string $resource = SiteMonitorResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
