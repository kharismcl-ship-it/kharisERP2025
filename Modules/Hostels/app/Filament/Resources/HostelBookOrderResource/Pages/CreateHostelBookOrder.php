<?php

namespace Modules\Hostels\Filament\Resources\HostelBookOrderResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\HostelBookOrderResource;

class CreateHostelBookOrder extends CreateRecord
{
    protected static string $resource = HostelBookOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
