<?php

namespace Modules\Construction\Filament\Resources\ContractorResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Construction\Filament\Resources\ContractorResource;

class EditContractor extends EditRecord
{
    protected static string $resource = ContractorResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
