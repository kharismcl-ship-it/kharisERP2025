<?php

namespace Modules\Farms\Filament\Resources\FarmCouponResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\FarmCouponResource;

class ListFarmCoupons extends ListRecords
{
    protected static string $resource = FarmCouponResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
