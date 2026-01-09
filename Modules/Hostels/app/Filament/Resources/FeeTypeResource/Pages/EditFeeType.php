<?php

namespace Modules\Hostels\Filament\Resources\FeeTypeResource\Pages;

    use Filament\Actions\DeleteAction;
    use Filament\Resources\Pages\EditRecord;
    use Modules\Hostels\Filament\Resources\FeeTypeResource;

    class EditFeeType extends EditRecord {
        protected static string $resource = FeeTypeResource::class;

        protected function getHeaderActions(): array {
        return [
        DeleteAction::make(),
        ];
        }
    }
