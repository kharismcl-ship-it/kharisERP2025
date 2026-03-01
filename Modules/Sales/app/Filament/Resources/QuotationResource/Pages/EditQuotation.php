<?php

namespace Modules\Sales\Filament\Resources\QuotationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\QuotationResource;

class EditQuotation extends EditRecord
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
