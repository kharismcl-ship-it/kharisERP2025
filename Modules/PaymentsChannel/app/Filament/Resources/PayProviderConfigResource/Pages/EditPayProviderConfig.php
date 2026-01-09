<?php

namespace Modules\PaymentsChannel\Filament\Resources\PayProviderConfigResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\PaymentsChannel\Filament\Resources\PayProviderConfigResource;

class EditPayProviderConfig extends EditRecord
{
    protected static string $resource = PayProviderConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
