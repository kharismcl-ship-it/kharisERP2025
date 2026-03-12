<?php

namespace Modules\Hostels\Filament\Resources\HostelMovieResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\HostelMovieResource;

class CreateHostelMovie extends CreateRecord
{
    protected static string $resource = HostelMovieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
