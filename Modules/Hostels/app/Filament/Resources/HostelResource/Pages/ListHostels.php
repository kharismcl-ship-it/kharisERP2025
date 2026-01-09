<?php

namespace Modules\Hostels\Filament\Resources\HostelResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelResource;

class ListHostels extends ListRecords
{
    protected static string $resource = HostelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
