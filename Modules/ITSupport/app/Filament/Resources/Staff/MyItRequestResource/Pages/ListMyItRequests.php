<?php

namespace Modules\ITSupport\Filament\Resources\Staff\MyItRequestResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ITSupport\Filament\Resources\Staff\MyItRequestResource;

class ListMyItRequests extends ListRecords
{
    protected static string $resource = MyItRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Submit IT Request'),
        ];
    }
}
