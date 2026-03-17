<?php

namespace Modules\Farms\Filament\Resources\FarmBundleResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmBundleResource;

class EditFarmBundle extends EditRecord
{
    protected static string $resource = FarmBundleResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
