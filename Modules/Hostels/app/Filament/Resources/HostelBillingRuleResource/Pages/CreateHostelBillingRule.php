<?php

namespace Modules\Hostels\Filament\Resources\HostelBillingRuleResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Modules\Hostels\Filament\Resources\HostelBillingRuleResource;

class CreateHostelBillingRule extends CreateRecord
{
    protected static string $resource = HostelBillingRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
