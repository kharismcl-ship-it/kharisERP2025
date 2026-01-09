<?php

namespace Modules\Hostels\Filament\Resources\HostelBlockResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\HostelBlockResource;

class CreateHostelBlock extends CreateRecord
{
    protected static string $resource = HostelBlockResource::class;

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
