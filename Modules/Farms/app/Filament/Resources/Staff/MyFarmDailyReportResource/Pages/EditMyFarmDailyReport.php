<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource;

class EditMyFarmDailyReport extends EditRecord
{
    protected static string $resource = MyFarmDailyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
