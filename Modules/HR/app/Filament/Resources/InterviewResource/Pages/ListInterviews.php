<?php

namespace Modules\HR\Filament\Resources\InterviewResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\InterviewResource;

class ListInterviews extends ListRecords
{
    protected static string $resource = InterviewResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
