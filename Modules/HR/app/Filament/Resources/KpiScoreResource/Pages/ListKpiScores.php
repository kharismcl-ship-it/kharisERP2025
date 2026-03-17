<?php

namespace Modules\HR\Filament\Resources\KpiScoreResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\KpiScoreResource;

class ListKpiScores extends ListRecords
{
    protected static string $resource = KpiScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
