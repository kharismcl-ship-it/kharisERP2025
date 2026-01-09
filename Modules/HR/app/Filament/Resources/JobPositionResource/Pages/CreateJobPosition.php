<?php

namespace Modules\HR\Filament\Resources\JobPositionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\HR\Filament\Resources\JobPositionResource;

class CreateJobPosition extends CreateRecord
{
    protected static string $resource = JobPositionResource::class;
}
