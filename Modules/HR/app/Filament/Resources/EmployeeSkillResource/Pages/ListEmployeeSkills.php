<?php

namespace Modules\HR\Filament\Resources\EmployeeSkillResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\EmployeeSkillResource;

class ListEmployeeSkills extends ListRecords
{
    protected static string $resource = EmployeeSkillResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
