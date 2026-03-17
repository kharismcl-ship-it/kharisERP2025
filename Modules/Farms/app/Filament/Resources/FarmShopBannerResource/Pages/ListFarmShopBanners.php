<?php

namespace Modules\Farms\Filament\Resources\FarmShopBannerResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmShopBannerResource;

class ListFarmShopBanners extends ListRecords
{
    protected static string $resource = FarmShopBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
