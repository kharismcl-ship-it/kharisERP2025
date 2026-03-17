<?php

namespace Modules\HR\Filament\Resources\Staff\MyCertificationResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\HR\Filament\Resources\Staff\MyCertificationResource;

class ViewMyCertification extends ViewRecord
{
    protected static string $resource = MyCertificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
