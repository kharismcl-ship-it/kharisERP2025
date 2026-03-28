<?php

namespace Modules\Farms\Filament\Resources\FarmGrowerPaymentResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmGrowerPaymentResource;

class EditFarmGrowerPayment extends EditRecord
{
    protected static string $resource = FarmGrowerPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}