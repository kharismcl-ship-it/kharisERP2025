<?php

namespace Modules\HR\Filament\Resources\GrievanceCaseResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\GrievanceCaseResource;

class ListGrievanceCases extends ListRecords
{
    protected static string $resource = GrievanceCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
