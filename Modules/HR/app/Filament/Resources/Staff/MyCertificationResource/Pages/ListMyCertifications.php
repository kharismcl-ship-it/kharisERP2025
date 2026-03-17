<?php

namespace Modules\HR\Filament\Resources\Staff\MyCertificationResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\Staff\MyCertificationResource;

class ListMyCertifications extends ListRecords
{
    protected static string $resource = MyCertificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Add Certification'),
        ];
    }
}
