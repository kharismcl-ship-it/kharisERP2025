<?php

namespace Modules\Sales\Filament\Resources\SalesOrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\SalesOrderResource;

class EditSalesOrder extends EditRecord
{
    protected static string $resource = SalesOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
