<?php

namespace Modules\HR\Filament\Resources\Staff\MySkillProfileResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\Staff\MySkillProfileResource;

class ListMySkills extends ListRecords
{
    protected static string $resource = MySkillProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}