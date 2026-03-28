<?php

namespace Modules\Farms\Filament\Resources\FarmAgronomistVisitResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmAgronomistVisitResource;

class EditFarmAgronomistVisit extends EditRecord
{
    protected static string $resource = FarmAgronomistVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}