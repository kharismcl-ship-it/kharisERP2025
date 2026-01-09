<?php

namespace Modules\PaymentsChannel\Filament\Resources\PayMethodResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\PaymentsChannel\Filament\Resources\PayMethodResource;

class ListPayMethods extends ListRecords
{
    protected static string $resource = PayMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
