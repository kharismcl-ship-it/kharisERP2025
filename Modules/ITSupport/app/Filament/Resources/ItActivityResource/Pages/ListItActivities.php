<?php

namespace Modules\ITSupport\Filament\Resources\ItActivityResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ITSupport\Filament\Resources\ItActivityResource;

class ListItActivities extends ListRecords
{
    protected static string $resource = ItActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
