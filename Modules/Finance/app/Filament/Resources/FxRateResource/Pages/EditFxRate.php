<?php

namespace Modules\Finance\Filament\Resources\FxRateResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Finance\Filament\Resources\FxRateResource;

class EditFxRate extends EditRecord
{
    protected static string $resource = FxRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}