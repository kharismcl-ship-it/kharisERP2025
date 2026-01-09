<?php

namespace Modules\Hostels\Filament\Resources\HostelBillingCycleResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Hostels\Filament\Resources\HostelBillingCycleResource;

class EditHostelBillingCycle extends EditRecord
{
    protected static string $resource = HostelBillingCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->url($this->getResource()::getUrl('index')),
            DeleteAction::make(),
        ];
    }
}
