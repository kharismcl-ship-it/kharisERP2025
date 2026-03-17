<?php

namespace Modules\Farms\Filament\Resources\HarvestRecordResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\HarvestRecordResource;

class ViewHarvestRecord extends ViewRecord
{
    protected static string $resource = HarvestRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make(), DeleteAction::make()];
    }
}
