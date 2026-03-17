<?php

namespace Modules\Farms\Filament\Resources\FarmShopNavItemResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmShopNavItemResource;

class EditFarmShopNavItem extends EditRecord
{
    protected static string $resource = FarmShopNavItemResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
