<?php

namespace Modules\HR\Filament\Resources\PublicHolidayResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\PublicHolidayResource;

class EditPublicHoliday extends EditRecord
{
    protected static string $resource = PublicHolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}