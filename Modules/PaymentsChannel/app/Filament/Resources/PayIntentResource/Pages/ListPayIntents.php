<?php

namespace Modules\PaymentsChannel\Filament\Resources\PayIntentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\PaymentsChannel\Filament\Resources\PayIntentResource;

class ListPayIntents extends ListRecords
{
    protected static string $resource = PayIntentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
