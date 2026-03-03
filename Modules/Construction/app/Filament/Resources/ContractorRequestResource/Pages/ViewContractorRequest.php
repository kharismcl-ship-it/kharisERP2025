<?php

namespace Modules\Construction\Filament\Resources\ContractorRequestResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Construction\Filament\Resources\ContractorRequestResource;

class ViewContractorRequest extends ViewRecord
{
    protected static string $resource = ContractorRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
