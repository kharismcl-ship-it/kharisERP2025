<?php

namespace Modules\Hostels\Filament\Resources\HostelResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Hostels\Filament\Resources\HostelResource;

class EditHostel extends EditRecord
{
    protected static string $resource = HostelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
