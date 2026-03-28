<?php

namespace Modules\Finance\Filament\Resources\PettyCashResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Finance\Filament\Resources\PettyCashResource;

class EditPettyCashFund extends EditRecord
{
    protected static string $resource = PettyCashResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}