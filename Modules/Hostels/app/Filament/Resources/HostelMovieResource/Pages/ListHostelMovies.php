<?php

namespace Modules\Hostels\Filament\Resources\HostelMovieResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelMovieResource;

class ListHostelMovies extends ListRecords
{
    protected static string $resource = HostelMovieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
