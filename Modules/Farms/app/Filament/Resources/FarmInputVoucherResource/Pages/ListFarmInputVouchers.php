<?php

namespace Modules\Farms\Filament\Resources\FarmInputVoucherResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmInputVoucherResource;

class ListFarmInputVouchers extends ListRecords
{
    protected static string $resource = FarmInputVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}