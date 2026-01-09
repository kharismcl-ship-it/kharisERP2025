<?php

namespace Modules\Hostels\Filament\Resources\WhatsAppGroupMessageResource\Pages;

use Modules\Hostels\Filament\Resources\WhatsAppGroupMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWhatsAppGroupMessages extends ListRecords
{
    protected static string $resource = WhatsAppGroupMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
