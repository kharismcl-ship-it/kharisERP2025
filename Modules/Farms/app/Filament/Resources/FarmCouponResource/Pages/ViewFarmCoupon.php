<?php

namespace Modules\Farms\Filament\Resources\FarmCouponResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmCouponResource;

class ViewFarmCoupon extends ViewRecord
{
    protected static string $resource = FarmCouponResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make(), DeleteAction::make()];
    }
}
