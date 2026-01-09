<?php

namespace Modules\Hostels\Filament\Resources\HostelTemplateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelTemplateResource;

class ListHostelTemplates extends ListRecords
{
    protected static string $resource = HostelTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
