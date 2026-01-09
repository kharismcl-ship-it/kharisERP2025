<?php

namespace Modules\PaymentsChannel\Filament\Resources\PayProviderConfigResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\PaymentsChannel\Filament\Resources\PayProviderConfigResource;

class ListPayProviderConfigs extends ListRecords
{
    protected static string $resource = PayProviderConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
