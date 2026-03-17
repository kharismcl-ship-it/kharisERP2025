<?php

namespace Modules\HR\Filament\Resources\Staff\MyTrainingResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\Staff\MyTrainingResource;

class ListMyTraining extends ListRecords
{
    protected static string $resource = MyTrainingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Request Training'),
        ];
    }
}
