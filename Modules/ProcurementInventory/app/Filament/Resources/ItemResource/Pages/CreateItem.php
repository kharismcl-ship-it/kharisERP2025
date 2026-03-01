<?php

namespace Modules\ProcurementInventory\Filament\Resources\ItemResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\ProcurementInventory\Filament\Resources\ItemResource;

class CreateItem extends CreateRecord
{
    protected static string $resource = ItemResource::class;
}