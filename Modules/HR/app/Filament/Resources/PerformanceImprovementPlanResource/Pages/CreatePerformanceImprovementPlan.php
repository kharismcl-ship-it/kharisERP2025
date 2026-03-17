<?php

namespace Modules\HR\Filament\Resources\PerformanceImprovementPlanResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\HR\Filament\Resources\PerformanceImprovementPlanResource;

class CreatePerformanceImprovementPlan extends CreateRecord
{
    protected static string $resource = PerformanceImprovementPlanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
