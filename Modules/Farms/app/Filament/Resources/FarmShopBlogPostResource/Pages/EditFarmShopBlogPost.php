<?php

namespace Modules\Farms\Filament\Resources\FarmShopBlogPostResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmShopBlogPostResource;

class EditFarmShopBlogPost extends EditRecord
{
    protected static string $resource = FarmShopBlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
