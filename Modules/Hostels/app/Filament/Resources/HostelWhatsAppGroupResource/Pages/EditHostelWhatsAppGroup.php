<?php

namespace Modules\Hostels\Filament\Resources\HostelWhatsAppGroupResource\Pages;

use Modules\Hostels\Filament\Resources\HostelWhatsAppGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHostelWhatsAppGroup extends EditRecord
{
    protected static string $resource = HostelWhatsAppGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
