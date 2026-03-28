<?php

namespace Modules\HR\Filament\Resources\EmployeeSkillResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\EmployeeSkillResource;

class EditEmployeeSkill extends EditRecord
{
    protected static string $resource = EmployeeSkillResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}