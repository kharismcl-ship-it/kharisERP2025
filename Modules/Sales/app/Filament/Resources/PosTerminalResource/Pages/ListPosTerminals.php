<?php

namespace Modules\Sales\Filament\Resources\PosTerminalResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Sales\Filament\Resources\PosTerminalResource;

class ListPosTerminals extends ListRecords
{
    protected static string $resource = PosTerminalResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
