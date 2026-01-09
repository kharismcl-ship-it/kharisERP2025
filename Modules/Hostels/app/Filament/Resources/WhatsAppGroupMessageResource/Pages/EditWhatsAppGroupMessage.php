<?php

namespace Modules\Hostels\Filament\Resources\WhatsAppGroupMessageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Hostels\Filament\Resources\WhatsAppGroupMessageResource;

class EditWhatsAppGroupMessage extends EditRecord
{
    protected static string $resource = WhatsAppGroupMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url($this->getResource()::getUrl('index')),
            Actions\DeleteAction::make(),
        ];
    }
}
