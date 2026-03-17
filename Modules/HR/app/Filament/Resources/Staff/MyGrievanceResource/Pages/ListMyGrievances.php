<?php

namespace Modules\HR\Filament\Resources\Staff\MyGrievanceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\Staff\MyGrievanceResource;

class ListMyGrievances extends ListRecords
{
    protected static string $resource = MyGrievanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Report Grievance'),
        ];
    }
}
