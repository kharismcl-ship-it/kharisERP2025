<?php

namespace Modules\Hostels\Filament\Resources\HostelBookOrderResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Hostels\Filament\Resources\HostelBookOrderResource;

class EditHostelBookOrder extends EditRecord
{
    protected static string $resource = HostelBookOrderResource::class;

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
