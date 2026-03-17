<?php

namespace Modules\HR\Filament\Resources\KpiScoreResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\HR\Filament\Resources\KpiScoreResource;

class CreateKpiScore extends CreateRecord
{
    protected static string $resource = KpiScoreResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['target_value']) && isset($data['actual_value']) && $data['target_value'] > 0) {
            $data['score'] = round(min(($data['actual_value'] / $data['target_value']) * 100, 100), 2);
        }

        return $data;
    }
}
