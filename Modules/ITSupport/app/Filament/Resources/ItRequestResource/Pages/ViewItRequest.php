<?php

namespace Modules\ITSupport\Filament\Resources\ItRequestResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\ITSupport\Filament\Resources\ItRequestResource;

class ViewItRequest extends ViewRecord
{
    protected static string $resource = ItRequestResource::class;

    protected string $view = 'itsupport::filament.resources.it-request-resource.pages.view-it-request';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
