<?php

namespace Modules\Sales\Filament\Resources\PosTerminalResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\PosTerminalResource;

class EditPosTerminal extends EditRecord
{
    protected static string $resource = PosTerminalResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
