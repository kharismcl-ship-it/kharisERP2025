<?php

namespace Modules\Farms\Filament\Resources\FarmCouponResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmCouponResource;

class EditFarmCoupon extends EditRecord
{
    protected static string $resource = FarmCouponResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make(), DeleteAction::make()];
    }
}
