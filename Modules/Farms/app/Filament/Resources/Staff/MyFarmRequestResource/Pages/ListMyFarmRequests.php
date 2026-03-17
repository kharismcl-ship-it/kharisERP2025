<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource;

class ListMyFarmRequests extends ListRecords
{
    protected static string $resource = MyFarmRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
