<?php

namespace Modules\Hostels\Filament\Resources\WhatsAppGroupMessageResource\Pages;

use Modules\Hostels\Filament\Resources\WhatsAppGroupMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWhatsAppGroupMessage extends EditRecord
{
    protected static string $resource = WhatsAppGroupMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
