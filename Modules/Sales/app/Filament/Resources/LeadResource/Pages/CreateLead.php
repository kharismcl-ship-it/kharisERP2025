<?php

namespace Modules\Sales\Filament\Resources\LeadResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Sales\Filament\Resources\LeadResource;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;
}
