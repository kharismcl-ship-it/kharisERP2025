<?php

namespace Modules\Hostels\Filament\Resources\BedResource\Pages;

    use Filament\Actions\DeleteAction;
    use Filament\Resources\Pages\EditRecord;
    use Modules\Hostels\Filament\Resources\BedResource;

    class EditBed extends EditRecord {
        protected static string $resource = BedResource::class;

        protected function getHeaderActions(): array {
        return [
        DeleteAction::make(),
        ];
        }
    }
