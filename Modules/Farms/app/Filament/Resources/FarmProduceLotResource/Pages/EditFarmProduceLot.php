<?php

namespace Modules\Farms\Filament\Resources\FarmProduceLotResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmProduceLotResource;

class EditFarmProduceLot extends EditRecord
{
    protected static string $resource = FarmProduceLotResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}