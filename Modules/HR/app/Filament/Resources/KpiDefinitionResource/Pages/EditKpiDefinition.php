<?php

namespace Modules\HR\Filament\Resources\KpiDefinitionResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\KpiDefinitionResource;

class EditKpiDefinition extends EditRecord
{
    protected static string $resource = KpiDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
