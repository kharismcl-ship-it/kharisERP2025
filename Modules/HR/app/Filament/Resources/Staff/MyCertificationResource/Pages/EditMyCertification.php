<?php

namespace Modules\HR\Filament\Resources\Staff\MyCertificationResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\Staff\MyCertificationResource;

class EditMyCertification extends EditRecord
{
    protected static string $resource = MyCertificationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
