<?php

namespace Modules\Farms\Filament\Resources\FarmInputCreditResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmInputCreditResource;

class EditFarmInputCredit extends EditRecord
{
    protected static string $resource = FarmInputCreditResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}