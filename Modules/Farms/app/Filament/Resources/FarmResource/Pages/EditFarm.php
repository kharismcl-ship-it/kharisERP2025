<?php

namespace Modules\Farms\Filament\Resources\FarmResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\FarmResource;

class EditFarm extends EditRecord
{
    protected static string $resource = FarmResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make()];
    }
}
