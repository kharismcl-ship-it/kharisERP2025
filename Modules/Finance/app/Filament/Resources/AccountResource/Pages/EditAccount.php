<?php

namespace Modules\Finance\Filament\Resources\AccountResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Finance\Filament\Resources\AccountResource;

class EditAccount extends EditRecord
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
