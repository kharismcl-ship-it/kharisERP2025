<?php

namespace Modules\Farms\Filament\Resources\FarmShopBannerResource\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Modules\Farms\Filament\Resources\FarmShopBannerResource;

class CreateFarmShopBanner extends CreateRecord
{
    protected static string $resource = FarmShopBannerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = Filament::getTenant()?->id;
        return $data;
    }
}
