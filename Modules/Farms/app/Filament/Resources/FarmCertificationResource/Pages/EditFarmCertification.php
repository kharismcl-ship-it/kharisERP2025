<?php

namespace Modules\Farms\Filament\Resources\FarmCertificationResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmCertificationResource;

class EditFarmCertification extends EditRecord
{
    protected static string $resource = FarmCertificationResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make(), DeleteAction::make()];
    }
}