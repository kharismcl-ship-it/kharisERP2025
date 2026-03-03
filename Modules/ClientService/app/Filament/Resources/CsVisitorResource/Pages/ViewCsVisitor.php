<?php

namespace Modules\ClientService\Filament\Resources\CsVisitorResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\ClientService\Filament\Resources\CsVisitorResource;
use Modules\CommunicationCentre\Concerns\HasCommunicationActions;

class ViewCsVisitor extends ViewRecord
{
    use HasCommunicationActions;

    protected static string $resource = CsVisitorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            ...$this->communicationActions(),
        ];
    }
}
