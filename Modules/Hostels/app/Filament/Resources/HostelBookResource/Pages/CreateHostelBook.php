<?php

namespace Modules\Hostels\Filament\Resources\HostelBookResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\HostelBookResource;

class CreateHostelBook extends CreateRecord
{
    protected static string $resource = HostelBookResource::class;

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
