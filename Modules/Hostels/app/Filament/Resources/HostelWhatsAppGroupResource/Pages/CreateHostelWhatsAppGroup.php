<?php

namespace Modules\Hostels\Filament\Resources\HostelWhatsAppGroupResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\HostelWhatsAppGroupResource;

class CreateHostelWhatsAppGroup extends CreateRecord
{
    protected static string $resource = HostelWhatsAppGroupResource::class;

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
