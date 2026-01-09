<?php

namespace Modules\Hostels\Filament\Resources\HostelBillingCycleResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\HostelBillingCycleResource;

class CreateHostelBillingCycle extends CreateRecord
{
    protected static string $resource = HostelBillingCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
