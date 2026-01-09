<?php

namespace Modules\Hostels\Filament\Resources\HostelChargeResource\Pages;

    use Filament\Actions\DeleteAction;
    use Filament\Resources\Pages\EditRecord;
    use Modules\Hostels\Filament\Resources\HostelChargeResource;

    class EditHostelCharge extends EditRecord {
        protected static string $resource = HostelChargeResource::class;

        protected function getHeaderActions(): array {
        return [
        DeleteAction::make(),
        ];
        }
    }
