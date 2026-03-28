<?php

namespace Modules\HR\Filament\Resources\OnboardingTaskResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\OnboardingTaskResource;

class EditOnboardingTask extends EditRecord
{
    protected static string $resource = OnboardingTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
