<?php

namespace Modules\PaymentsChannel\Filament\Resources\PayMethodResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\PaymentsChannel\Filament\Resources\PayMethodResource;

class EditPayMethod extends EditRecord
{
    protected static string $resource = PayMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
