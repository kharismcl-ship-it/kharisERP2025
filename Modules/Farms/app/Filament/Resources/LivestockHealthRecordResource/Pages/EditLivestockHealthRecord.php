<?php

namespace Modules\Farms\Filament\Resources\LivestockHealthRecordResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\LivestockHealthRecordResource;

class EditLivestockHealthRecord extends EditRecord
{
    protected static string $resource = LivestockHealthRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}