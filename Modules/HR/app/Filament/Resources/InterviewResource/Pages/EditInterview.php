<?php

namespace Modules\HR\Filament\Resources\InterviewResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\InterviewResource;

class EditInterview extends EditRecord
{
    protected static string $resource = InterviewResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
