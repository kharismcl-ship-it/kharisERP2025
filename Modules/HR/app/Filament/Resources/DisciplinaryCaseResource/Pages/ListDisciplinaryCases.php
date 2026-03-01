<?php

namespace Modules\HR\Filament\Resources\DisciplinaryCaseResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\DisciplinaryCaseResource;

class ListDisciplinaryCases extends ListRecords
{
    protected static string $resource = DisciplinaryCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}