<?php

namespace Modules\Hostels\Filament\Resources\HostelStaffResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelStaffResource;

class ListHostelStaffs extends ListRecords
{
    protected static string $resource = HostelStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
