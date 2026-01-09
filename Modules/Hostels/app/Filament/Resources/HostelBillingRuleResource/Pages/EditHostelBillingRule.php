<?php

namespace Modules\Hostels\Filament\Resources\HostelBillingRuleResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Hostels\Filament\Resources\HostelBillingRuleResource;

class EditHostelBillingRule extends EditRecord
{
    protected static string $resource = HostelBillingRuleResource::class;

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
