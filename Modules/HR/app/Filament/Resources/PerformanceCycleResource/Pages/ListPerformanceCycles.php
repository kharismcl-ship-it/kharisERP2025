<?php

namespace Modules\HR\Filament\Resources\PerformanceCycleResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\PerformanceCycleResource;
use Filament\Actions\CreateAction;

class ListPerformanceCycles extends ListRecords
{
    protected static string $resource = PerformanceCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
