<?php

namespace Modules\CommunicationCentre\Filament\Resources\WebhookResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\CommunicationCentre\Filament\Resources\WebhookResource;

class ListWebhooks extends ListRecords
{
    protected static string $resource = WebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
