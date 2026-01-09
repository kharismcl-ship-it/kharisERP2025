<?php

namespace Modules\Hostels\Filament\Resources\HostelFloorResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\HostelFloorResource;

class CreateHostelFloor extends CreateRecord
{
    protected static string $resource = HostelFloorResource::class;

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
