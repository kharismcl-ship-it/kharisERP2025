<?php

namespace Modules\HR\Filament\Resources\OnboardingTaskResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\OnboardingTaskResource;

class ListOnboardingTasks extends ListRecords
{
    protected static string $resource = OnboardingTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}