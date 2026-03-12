<?php

namespace Modules\Hostels\Filament\Resources\HostelMovieRequestResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelMovieRequestResource;

class ListHostelMovieRequests extends ListRecords
{
    protected static string $resource = HostelMovieRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
