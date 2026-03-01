<?php

namespace Modules\HR\Filament\Resources\PublicHolidayResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\PublicHolidayResource;

class ListPublicHolidays extends ListRecords
{
    protected static string $resource = PublicHolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->slideOver(),
        ];
    }
}