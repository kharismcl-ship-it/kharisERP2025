<?php

namespace Modules\Farms\Filament\Resources\FarmInputVoucherResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmInputVoucherResource;

class EditFarmInputVoucher extends EditRecord
{
    protected static string $resource = FarmInputVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}