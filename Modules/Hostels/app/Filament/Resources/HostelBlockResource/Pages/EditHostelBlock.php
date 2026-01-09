<?php

namespace Modules\Hostels\Filament\Resources\HostelBlockResource\Pages;

use Filament\Actions;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Hostels\Filament\Resources\HostelBlockResource;

class EditHostelBlock extends EditRecord
{
    protected static string $resource = HostelBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->url($this->getResource()::getUrl('index')),
            DeleteAction::make(),
        ];
    }
}
