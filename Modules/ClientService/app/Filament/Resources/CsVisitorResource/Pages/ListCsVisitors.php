<?php

namespace Modules\ClientService\Filament\Resources\CsVisitorResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ClientService\Filament\Resources\CsVisitorResource;

class ListCsVisitors extends ListRecords
{
    protected static string $resource = CsVisitorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
