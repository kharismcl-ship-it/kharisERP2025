<?php

namespace Modules\Farms\Filament\Resources\FarmShopBannerResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmShopBannerResource;

class EditFarmShopBanner extends EditRecord
{
    protected static string $resource = FarmShopBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
