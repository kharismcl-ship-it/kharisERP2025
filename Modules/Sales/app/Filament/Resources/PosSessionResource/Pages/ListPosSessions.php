<?php

namespace Modules\Sales\Filament\Resources\PosSessionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Sales\Filament\Resources\PosSessionResource;

class ListPosSessions extends ListRecords
{
    protected static string $resource = PosSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
