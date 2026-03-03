<?php

namespace Modules\Construction\Filament\Resources\ContractorRequestResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Construction\Filament\Resources\ContractorRequestResource;

class EditContractorRequest extends EditRecord
{
    protected static string $resource = ContractorRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
