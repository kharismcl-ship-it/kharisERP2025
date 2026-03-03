<?php

namespace Modules\ITSupport\Filament\Resources\ItRequestResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ITSupport\Filament\Resources\ItRequestResource;

class EditItRequest extends EditRecord
{
    protected static string $resource = ItRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
