<?php

namespace Modules\Farms\Filament\Resources\FarmSaleResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmSaleResource;

class EditFarmSale extends EditRecord
{
    protected static string $resource = FarmSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make(), DeleteAction::make()];
    }
}
