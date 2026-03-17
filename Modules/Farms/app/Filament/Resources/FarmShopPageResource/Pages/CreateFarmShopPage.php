<?php

namespace Modules\Farms\Filament\Resources\FarmShopPageResource\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Modules\Farms\Filament\Resources\FarmShopPageResource;

class CreateFarmShopPage extends CreateRecord
{
    protected static string $resource = FarmShopPageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = Filament::getTenant()?->id;
        return $data;
    }
}
