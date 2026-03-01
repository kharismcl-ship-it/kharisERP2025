<?php

namespace Modules\CommunicationCentre\Filament\Resources\WebhookResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\CommunicationCentre\Filament\Resources\WebhookResource;

class CreateWebhook extends CreateRecord
{
    protected static string $resource = WebhookResource::class;
}
