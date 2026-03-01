<?php

namespace Modules\ManufacturingPaper\Filament\Resources\MpPaperGradeResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ManufacturingPaper\Filament\Resources\MpPaperGradeResource;

class EditMpPaperGrade extends EditRecord
{
    protected static string $resource = MpPaperGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
