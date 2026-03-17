<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource;

class ViewMyFarmDailyReport extends ViewRecord
{
    protected static string $resource = MyFarmDailyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => $this->record->status === 'draft'),
            DeleteAction::make()
                ->visible(fn () => $this->record->status === 'draft'),
        ];
    }
}
