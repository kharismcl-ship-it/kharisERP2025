<?php

namespace Modules\Sales\Filament\Resources\PosSessionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\PosSessionResource;

class EditPosSession extends EditRecord
{
    protected static string $resource = PosSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
