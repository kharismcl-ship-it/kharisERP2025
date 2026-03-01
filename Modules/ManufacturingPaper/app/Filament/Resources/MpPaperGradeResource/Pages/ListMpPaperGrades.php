<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpPaperGradeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ManufacturingPaper\Filament\Resources\MpPaperGradeResource;

class ListMpPaperGrades extends ListRecords
{
    protected static string $resource = MpPaperGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
