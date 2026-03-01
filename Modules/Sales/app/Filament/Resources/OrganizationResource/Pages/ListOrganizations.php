<?php

namespace Modules\Sales\Filament\Resources\OrganizationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Sales\Filament\Resources\OrganizationResource;

class ListOrganizations extends ListRecords
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
