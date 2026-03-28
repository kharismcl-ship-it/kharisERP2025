<?php

namespace Modules\Farms\Filament\Resources\FarmCertificationResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmCertificationResource;

class ViewFarmCertification extends ViewRecord
{
    protected static string $resource = FarmCertificationResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make(), DeleteAction::make()];
    }
}