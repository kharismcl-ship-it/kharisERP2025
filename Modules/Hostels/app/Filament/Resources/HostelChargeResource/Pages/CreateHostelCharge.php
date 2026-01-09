<?php

namespace Modules\Hostels\Filament\Resources\HostelChargeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\HostelChargeResource;

class CreateHostelCharge extends CreateRecord
{
    protected static string $resource = HostelChargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
