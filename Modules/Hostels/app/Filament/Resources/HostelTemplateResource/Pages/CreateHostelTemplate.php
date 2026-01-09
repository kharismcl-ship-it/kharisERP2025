<?php

namespace Modules\Hostels\Filament\Resources\HostelTemplateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\HostelTemplateResource;

class CreateHostelTemplate extends CreateRecord
{
    protected static string $resource = HostelTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
