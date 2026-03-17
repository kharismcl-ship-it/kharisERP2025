<?php

namespace Modules\Farms\Filament\Resources\FarmShopBlogPostResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmShopBlogPostResource;

class ListFarmShopBlogPosts extends ListRecords
{
    protected static string $resource = FarmShopBlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
