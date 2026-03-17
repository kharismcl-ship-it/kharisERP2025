<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource;

class ViewMyVisitorLog extends ViewRecord
{
    protected static string $resource = MyVisitorLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
