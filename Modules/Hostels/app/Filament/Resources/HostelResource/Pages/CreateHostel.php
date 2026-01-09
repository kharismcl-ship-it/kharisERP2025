<?php

namespace Modules\Hostels\Filament\Resources\HostelResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\HostelResource;

class CreateHostel extends CreateRecord
{
    protected static string $resource = HostelResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
