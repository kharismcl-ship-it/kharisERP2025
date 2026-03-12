<?php

namespace Modules\Hostels\Filament\Resources\HostelMovieResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Hostels\Filament\Resources\HostelMovieResource;

class EditHostelMovie extends EditRecord
{
    protected static string $resource = HostelMovieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url($this->getResource()::getUrl('index')),
            DeleteAction::make(),
        ];
    }
}
