<?php

namespace Modules\Farms\Filament\Resources\HarvestRecordResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\HarvestRecordResource;

class EditHarvestRecord extends EditRecord
{
    protected static string $resource = HarvestRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make(), DeleteAction::make()];
    }
}
