<?php

namespace Modules\Farms\Filament\Resources\FarmShopBlogPostResource\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Modules\Farms\Filament\Resources\FarmShopBlogPostResource;

class CreateFarmShopBlogPost extends CreateRecord
{
    protected static string $resource = FarmShopBlogPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = Filament::getTenant()?->id ?? auth()->user()?->current_company_id;
        return $data;
    }
}
