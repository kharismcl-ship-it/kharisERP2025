<?php

namespace Modules\Farms\Filament\Resources\FarmShopPageResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmShopPageResource;

class EditFarmShopPage extends EditRecord
{
    protected static string $resource = FarmShopPageResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
