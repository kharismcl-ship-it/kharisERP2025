<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyIncidentResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Hostels\Filament\Resources\Staff\MyIncidentResource;

class ViewMyIncident extends ViewRecord
{
    protected static string $resource = MyIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => $this->record->status === 'open'),
        ];
    }
}
