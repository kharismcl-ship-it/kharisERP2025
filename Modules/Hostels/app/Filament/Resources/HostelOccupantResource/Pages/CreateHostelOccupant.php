<?php

namespace Modules\Hostels\Filament\Resources\HostelOccupantResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\HostelOccupantResource;

class CreateHostelOccupant extends CreateRecord
{
    protected static string $resource = HostelOccupantResource::class;

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
