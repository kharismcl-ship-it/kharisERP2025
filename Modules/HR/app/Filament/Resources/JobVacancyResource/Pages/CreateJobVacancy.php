<?php

namespace Modules\HR\Filament\Resources\JobVacancyResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\HR\Filament\Resources\JobVacancyResource;

class CreateJobVacancy extends CreateRecord
{
    protected static string $resource = JobVacancyResource::class;
}