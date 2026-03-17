<?php

namespace Modules\Farms\Filament\Resources\FarmShopNavItemResource\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Modules\Farms\Filament\Resources\FarmShopNavItemResource;

class CreateFarmShopNavItem extends CreateRecord
{
    protected static string $resource = FarmShopNavItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = Filament::getTenant()?->id;
        return $data;
    }
}
