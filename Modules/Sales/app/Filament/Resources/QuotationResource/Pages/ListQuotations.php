<?php

namespace Modules\Sales\Filament\Resources\QuotationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Sales\Filament\Resources\QuotationResource;

class ListQuotations extends ListRecords
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
