<?php

namespace Modules\Hostels\Filament\Resources\HostelInventoryItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\HostelInventoryItemResource;

class CreateHostelInventoryItem extends CreateRecord
{
    protected static string $resource = HostelInventoryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
