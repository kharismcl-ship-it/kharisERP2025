<?php

namespace Modules\ClientService\Filament\Resources\CsVisitorResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\ClientService\Filament\Resources\CsVisitorResource;

class EditCsVisitor extends EditRecord
{
    protected static string $resource = CsVisitorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
