<?php

namespace Modules\PaymentsChannel\Filament\Resources\PayTransactionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\PaymentsChannel\Filament\Resources\PayTransactionResource;

class EditPayTransaction extends EditRecord
{
    protected static string $resource = PayTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
