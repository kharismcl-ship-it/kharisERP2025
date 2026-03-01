<?php

namespace Modules\Sales\Filament\Resources\DiningOrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\DiningOrderResource;

class EditDiningOrder extends EditRecord
{
    protected static string $resource = DiningOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
