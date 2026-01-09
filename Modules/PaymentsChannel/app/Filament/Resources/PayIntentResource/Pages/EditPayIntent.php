<?php

namespace Modules\PaymentsChannel\Filament\Resources\PayIntentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\PaymentsChannel\Filament\Resources\PayIntentResource;

class EditPayIntent extends EditRecord
{
    protected static string $resource = PayIntentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
