<?php

namespace Modules\Farms\Filament\Resources\FarmTrialResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmTrialResource;

class EditFarmTrial extends EditRecord
{
    protected static string $resource = FarmTrialResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}