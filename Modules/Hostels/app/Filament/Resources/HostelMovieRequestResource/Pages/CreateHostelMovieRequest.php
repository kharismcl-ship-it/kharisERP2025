<?php

namespace Modules\Hostels\Filament\Resources\HostelMovieRequestResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\HostelMovieRequestResource;

class CreateHostelMovieRequest extends CreateRecord
{
    protected static string $resource = HostelMovieRequestResource::class;

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
