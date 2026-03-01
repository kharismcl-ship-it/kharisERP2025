<?php

namespace Modules\Farms\Filament\Resources\LivestockBatchResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\LivestockBatchResource;

class EditLivestockBatch extends EditRecord
{
    protected static string $resource = LivestockBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}