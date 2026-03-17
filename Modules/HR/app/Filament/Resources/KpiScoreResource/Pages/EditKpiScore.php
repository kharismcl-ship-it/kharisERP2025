<?php

namespace Modules\HR\Filament\Resources\KpiScoreResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\KpiScoreResource;

class EditKpiScore extends EditRecord
{
    protected static string $resource = KpiScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['target_value']) && isset($data['actual_value']) && $data['target_value'] > 0) {
            $data['score'] = round(min(($data['actual_value'] / $data['target_value']) * 100, 100), 2);
        }

        return $data;
    }
}
