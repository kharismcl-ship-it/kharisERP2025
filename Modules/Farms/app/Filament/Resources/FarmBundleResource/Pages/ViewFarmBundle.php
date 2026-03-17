<?php

namespace Modules\Farms\Filament\Resources\FarmBundleResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmBundleResource;

class ViewFarmBundle extends ViewRecord
{
    protected static string $resource = FarmBundleResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
