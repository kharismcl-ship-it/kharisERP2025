<?php

namespace Modules\Hostels\Filament\Resources\HostelStaffRoleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelStaffRoleResource;

class ListHostelStaffRoles extends ListRecords
{
    protected static string $resource = HostelStaffRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
