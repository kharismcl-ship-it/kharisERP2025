<?php

namespace Modules\HR\Filament\Resources\PerformanceImprovementPlanResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\PerformanceImprovementPlanResource;

class EditPerformanceImprovementPlan extends EditRecord
{
    protected static string $resource = PerformanceImprovementPlanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
