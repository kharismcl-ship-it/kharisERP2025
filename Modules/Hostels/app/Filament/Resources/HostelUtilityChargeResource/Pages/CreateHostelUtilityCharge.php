<?php

namespace Modules\Hostels\Filament\Resources\HostelUtilityChargeResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\HostelUtilityChargeResource;

class CreateHostelUtilityCharge extends CreateRecord
{
    protected static string $resource = HostelUtilityChargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
