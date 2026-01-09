<?php

namespace Modules\Hostels\Filament\Resources\VisitorLogResource\Pages;

    use Filament\Actions\DeleteAction;
    use Filament\Resources\Pages\EditRecord;
    use Modules\Hostels\Filament\Resources\VisitorLogResource;

    class EditVisitorLog extends EditRecord {
        protected static string $resource = VisitorLogResource::class;

        protected function getHeaderActions(): array {
        return [
        DeleteAction::make(),
        ];
        }
    }
