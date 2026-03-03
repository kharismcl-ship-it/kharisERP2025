<?php

namespace Modules\ITSupport\Filament\Resources\ItRequestResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ITSupport\Filament\Resources\ItRequestResource;

class ListItRequests extends ListRecords
{
    protected static string $resource = ItRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
