<?php

namespace Modules\ITSupport\Filament\Resources\ItActivityResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ITSupport\Filament\Resources\ItActivityResource;

class EditItActivity extends EditRecord
{
    protected static string $resource = ItActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
